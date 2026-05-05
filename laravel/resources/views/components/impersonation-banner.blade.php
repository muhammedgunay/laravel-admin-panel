@props(['impersonatorName', 'currentUserName'])

<div style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: #d97706;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 16px;
    font-size: 12px;
    font-family: ui-sans-serif, system-ui, sans-serif;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
">
    <span style="display:flex;align-items:center;gap:6px;min-width:0;overflow:hidden;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span><strong>{{ $currentUserName }}</strong> olarak işlem yapıyorsunuz &mdash; orijinal hesap: {{ $impersonatorName }}</span>
    </span>

    <form action="{{ route('impersonate.leave') }}" method="POST" style="flex-shrink:0">
        @csrf
        <button type="submit" style="
            background: rgba(0,0,0,.25);
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            white-space: nowrap;
        " onmouseover="this.style.background='rgba(0,0,0,.4)'" onmouseout="this.style.background='rgba(0,0,0,.25)'">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
            </svg>
            Geri Dön
        </button>
    </form>
</div>

{{-- Banner yüksekliği kadar boşluk bırak --}}
<div style="height:33px"></div>
