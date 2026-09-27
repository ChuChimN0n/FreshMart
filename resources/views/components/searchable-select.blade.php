@props([
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => '-- Chọn --',
    'searchPlaceholder' => 'Gõ để tìm kiếm...',
    'required' => false,
    'autosubmit' => false,
    'label' => null,
    'error' => null,
])
@php
    $uid = 'ss-'.str_replace('.', '-', $name).'-'.uniqid();
    $selectedLabel = '';
    foreach ($options as $opt) {
        if (isset($opt['value']) && (string) $opt['value'] === (string) $selected && $selected !== null && $selected !== '') {
            $selectedLabel = $opt['label'] ?? '';
            break;
        }
    }
    // Option rỗng (VD: "Tất cả...") được coi như chưa chọn
    $hasValue = $selected !== null && $selected !== '';
@endphp
<div data-searchable-select @if($autosubmit) data-autosubmit @endif>
    @if($label)
        <label for="{{ $uid }}-input" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <input type="hidden" name="{{ $name }}" value="{{ $selected }}" data-ss-value>
    <div class="relative">
        <input type="text" id="{{ $uid }}-input" value="{{ $selectedLabel }}" placeholder="{{ $selectedLabel ?: $placeholder }}"
               autocomplete="off" data-ss-input
               class="bhx-input pr-16 {{ $error ? '!border-red-500' : '' }}">
        <span class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
            <button type="button" data-ss-clear title="Xóa lựa chọn"
                    class="{{ $hasValue ? 'inline-flex' : 'hidden' }} w-6 h-6 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 items-center justify-center">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
            <i class="bi bi-chevron-down text-gray-400 text-xs pointer-events-none"></i>
        </span>
        <ul data-ss-list
            class="hidden absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto z-50 py-1 text-sm">
            @forelse($options as $opt)
                <li data-ss-item data-value="{{ $opt['value'] }}" data-label="{{ $opt['label'] }}"
                    class="px-3 py-2 cursor-pointer hover:bg-bhx-50 flex items-center justify-between gap-2 {{ (string) ($opt['value'] ?? '') === (string) $selected ? 'text-bhx-700 font-medium' : 'text-gray-700' }}">
                    <span class="truncate">{{ $opt['label'] }}</span>
                    <i class="bi bi-check-lg text-bhx-600 {{ (string) ($opt['value'] ?? '') === (string) $selected ? '' : 'invisible' }}"></i>
                </li>
            @empty
                <li class="px-3 py-2 text-gray-400">Chưa có dữ liệu</li>
            @endforelse
            <li data-ss-empty class="hidden px-3 py-2 text-gray-400">Không tìm thấy kết quả</li>
        </ul>
    </div>
    <p data-ss-required-hint class="hidden text-red-500 text-sm mt-1">Vui lòng chọn một giá trị.</p>
    @if($error) <p class="text-red-500 text-sm mt-1">{{ $error }}</p> @endif
</div>

<script>
(function () {
    const root = document.currentScript.previousElementSibling;
    const valueInput = root.querySelector('[data-ss-value]');
    const textInput = root.querySelector('[data-ss-input]');
    const list = root.querySelector('[data-ss-list]');
    const items = Array.from(root.querySelectorAll('[data-ss-item]'));
    const emptyRow = root.querySelector('[data-ss-empty]');
    const clearBtn = root.querySelector('[data-ss-clear]');
    const requiredHint = root.querySelector('[data-ss-required-hint]');
    const isRequired = {{ $required ? 'true' : 'false' }};
    const autoSubmit = root.hasAttribute('data-autosubmit');
    const form = root.closest('form');
    let highlightIndex = -1;

    const norm = (s) => (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');

    function openList() { list.classList.remove('hidden'); filter(textInput.value); }
    function closeList() { list.classList.add('hidden'); highlightIndex = -1; paintHighlight(); }

    function visibleItems() { return items.filter((li) => !li.classList.contains('hidden') && !li.hasAttribute('data-ss-empty')); }

    function filter(keyword) {
        const q = norm(keyword.trim());
        let shown = 0;
        items.forEach((li) => {
            const hit = !q || norm(li.dataset.label).includes(q);
            li.classList.toggle('hidden', !hit);
            if (hit) shown++;
        });
        if (emptyRow) emptyRow.classList.toggle('hidden', shown > 0);
        highlightIndex = -1;
        paintHighlight();
    }

    function paintHighlight() {
        const vis = visibleItems();
        vis.forEach((li, i) => li.classList.toggle('bg-bhx-50', i === highlightIndex));
    }

    function choose(li) {
        if (!li) return;
        valueInput.value = li.dataset.value;
        textInput.value = li.dataset.label;
        textInput.placeholder = li.dataset.label;
        items.forEach((it) => {
            const active = it === li;
            it.classList.toggle('text-bhx-700', active);
            it.classList.toggle('font-medium', active);
            it.classList.toggle('text-gray-700', !active);
            it.querySelector('i').classList.toggle('invisible', !active);
        });
        const emptyChoice = !li.dataset.value;
        clearBtn.classList.toggle('hidden', emptyChoice);
        clearBtn.classList.toggle('inline-flex', !emptyChoice);
        requiredHint.classList.add('hidden');
        textInput.classList.remove('!border-red-500');
        valueInput.dispatchEvent(new Event('change', { bubbles: true }));
        closeList();
        if (autoSubmit && form) form.submit();
    }

    function clearAll() {
        valueInput.value = '';
        textInput.value = '';
        items.forEach((it) => {
            it.classList.remove('text-bhx-700', 'font-medium');
            it.classList.add('text-gray-700');
            it.querySelector('i').classList.add('invisible');
        });
        clearBtn.classList.add('hidden');
        clearBtn.classList.remove('inline-flex');
        filter('');
        textInput.focus();
        valueInput.dispatchEvent(new Event('change', { bubbles: true }));
        if (autoSubmit && form) form.submit();
    }

    textInput.addEventListener('focus', openList);
    textInput.addEventListener('click', openList);
    textInput.addEventListener('input', () => { openList(); });
    textInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeList(); textInput.blur(); }
        else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            const vis = visibleItems();
            if (!vis.length) return;
            highlightIndex = e.key === 'ArrowDown'
                ? (highlightIndex + 1) % vis.length
                : (highlightIndex - 1 + vis.length) % vis.length;
            paintHighlight();
            vis[highlightIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            const vis = visibleItems();
            if (!list.classList.contains('hidden') && vis.length) {
                e.preventDefault();
                choose(vis[Math.max(highlightIndex, 0)]);
            }
        }
    });

    list.addEventListener('mousedown', (e) => {
        const li = e.target.closest('[data-ss-item]');
        if (li && !li.classList.contains('hidden')) {
            e.preventDefault();
            choose(li);
        }
    });

    document.addEventListener('click', (e) => { if (!root.contains(e.target)) closeList(); });

    clearBtn.addEventListener('click', (e) => { e.stopPropagation(); clearAll(); });

    if (form && isRequired) {
        form.addEventListener('submit', (e) => {
            if (!valueInput.value) {
                e.preventDefault();
                requiredHint.classList.remove('hidden');
                textInput.classList.add('!border-red-500');
                textInput.focus();
                openList();
            }
        });
    }
})();
</script>
