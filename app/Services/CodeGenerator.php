<?php

namespace App\Services;

use App\Models\DanhMuc;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DuplicateCodeException extends \RuntimeException
{
    //
}

class CodeGenerator
{
    public const MAX_ATTEMPTS = 3;

    /**
     * Sinh mã kế tiếp cho một profile (VD: RAU-0047, DH-202609-0001).
     *
     * @param  array<string, mixed>  $context  VD: ['maDM' => 3, 'date' => '2026-09-27']
     *
     * @throws \InvalidArgumentException
     * @throws DuplicateCodeException
     */
    public static function next(string $profile, array $context = []): string
    {
        $cfg = config("codes.profiles.{$profile}");
        if (! is_array($cfg)) {
            throw new \InvalidArgumentException("Unknown code profile [{$profile}].");
        }

        $prefix = self::resolvePrefix($cfg, $context);

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $candidate = self::format($cfg, $prefix, self::maxSuffix($cfg, $prefix) + 1);
            if (! self::exists($cfg, $candidate)) {
                return $candidate;
            }
        }

        throw new DuplicateCodeException("Không thể sinh mã duy nhất cho profile [{$profile}] với prefix [{$prefix}].");
    }

    /**
     * Chạy callback insert, tự sinh lại mã và thử lại khi dính trùng unique.
     *
     * @template T
     *
     * @param  callable(): T  $insert  Callback tự sinh mã mới rồi insert trong mỗi lần thử.
     * @return T
     *
     * @throws \Throwable
     */
    public static function insertUnique(callable $insert, string $column): mixed
    {
        $last = null;
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            try {
                return $insert();
            } catch (QueryException $e) {
                $last = $e;
                if (! self::isDuplicateOn($e, $column)) {
                    throw $e;
                }
            }
        }

        throw $last;
    }

    private static function isDuplicateOn(QueryException $e, string $column): bool
    {
        // MySQL: SQLSTATE 23000 + "Duplicate entry ... for key '...sku...'"
        // SQLite: SQLSTATE 23000 + "UNIQUE constraint failed: ...sku"
        return $e->getCode() === '23000'
            && str_contains(strtolower($e->getMessage()), strtolower($column));
    }

    /**
     * Kiểm tra một mã có đúng định dạng của profile không.
     */
    public static function isValid(string $profile, ?string $code): bool
    {
        $pattern = config("codes.profiles.{$profile}.pattern");
        if (! is_string($pattern) || ! is_string($code)) {
            return false;
        }

        return (bool) preg_match($pattern, $code);
    }

    /**
     * @param  array<string, mixed>  $cfg
     * @param  array<string, mixed>  $context
     */
    private static function resolvePrefix(array $cfg, array $context): string
    {
        $prefix = $cfg['prefix'] ?? ['type' => 'fixed', 'value' => 'X'];

        return match ($prefix['type'] ?? 'fixed') {
            'category' => self::categoryPrefix($context[$prefix['context_key'] ?? 'maDM'] ?? null, (int) ($prefix['length'] ?? 3)),
            'dated' => ($prefix['format'] ?? 'DH').'-'.self::contextDate($context)->format($prefix['date_format'] ?? 'Ym'),
            default => (string) ($prefix['value'] ?? 'X'),
        };
    }

    private static function categoryPrefix(mixed $maDM, int $length): string
    {
        $tenDM = $maDM ? DanhMuc::whereKey($maDM)->value('tenDM') : null;
        $ascii = strtoupper(Str::ascii((string) $tenDM));
        $code = substr(preg_replace('/[^A-Z0-9]/', '', $ascii), 0, max($length, 1));

        return $code !== '' ? $code : 'SP';
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private static function contextDate(array $context): Carbon
    {
        if (! empty($context['date'])) {
            return Carbon::parse($context['date']);
        }

        return Carbon::now();
    }

    /**
     * @param  array<string, mixed>  $cfg
     */
    private static function baseQuery(array $cfg, string $prefix): Builder
    {
        return DB::table($cfg['table'])->where($cfg['column'], 'like', $prefix.$cfg['separator'].'%');
    }

    /**
     * @param  array<string, mixed>  $cfg
     */
    private static function maxSuffix(array $cfg, string $prefix): int
    {
        $codes = self::baseQuery($cfg, $prefix)->pluck($cfg['column']);
        $max = 0;
        foreach ($codes as $code) {
            $pos = strrpos((string) $code, $cfg['separator']);
            if ($pos === false) {
                continue;
            }
            $suffix = substr((string) $code, $pos + strlen($cfg['separator']));
            if (ctype_digit($suffix)) {
                $max = max($max, (int) $suffix);
            }
        }

        return $max;
    }

    /**
     * @param  array<string, mixed>  $cfg
     */
    private static function exists(array $cfg, string $candidate): bool
    {
        return DB::table($cfg['table'])->where($cfg['column'], $candidate)->exists();
    }

    /**
     * @param  array<string, mixed>  $cfg
     */
    private static function format(array $cfg, string $prefix, int $number): string
    {
        return $prefix.$cfg['separator'].str_pad((string) $number, (int) ($cfg['pad'] ?? 4), '0', STR_PAD_LEFT);
    }
}
