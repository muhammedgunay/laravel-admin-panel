<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $announcements = $this->getAnnouncements();
        @endphp

        @if($announcements->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-gray-400 dark:text-gray-500">
                <span class="text-4xl mb-2">📭</span>
                <p class="text-sm font-medium">No active announcements</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($announcements as $announcement)
                    @php
                        $bgClass   = \App\Filament\Widgets\AnnouncementsWidget::getTypeBg($announcement->type);
                        $textClass = \App\Filament\Widgets\AnnouncementsWidget::getTypeTextColor($announcement->type);
                        $icon      = \App\Filament\Widgets\AnnouncementsWidget::getTypeIcon($announcement->type);
                        $priority  = \App\Filament\Widgets\AnnouncementsWidget::getPriorityLabel($announcement->priority);
                    @endphp

                    <div class="relative rounded-xl border-l-4 p-4 shadow-sm transition-all hover:shadow-md {{ $bgClass }}">

                        {{-- Pinned badge --}}
                        @if($announcement->is_pinned)
                            <span class="absolute top-3 right-3 text-xs font-semibold px-2 py-0.5 rounded-full bg-white/70 dark:bg-black/30 text-gray-600 dark:text-gray-300">
                                📌 Pinned
                            </span>
                        @endif

                        {{-- Header --}}
                        <div class="flex items-start gap-3">
                            <span class="text-2xl leading-none">{{ $icon }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-sm {{ $textClass }} truncate">
                                        {{ $announcement->title }}
                                    </h3>
                                    @if($announcement->priority >= 4)
                                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">
                                            {{ $priority }}
                                        </span>
                                    @endif
                                </div>

                                @if($announcement->description)
                                    <div x-data="{ expanded: false }">
                                        <div class="mt-1 text-xs {{ $textClass }} opacity-80" 
                                           :class="expanded ? '' : 'line-clamp-2'">
                                            {!! nl2br(e($announcement->description)) !!}
                                        </div>
                                        @if(strlen($announcement->description) > 100)
                                            <button @click="expanded = !expanded" class="text-[10px] font-bold mt-1 opacity-100 hover:underline" x-text="expanded ? 'Daha Az Göster' : 'Devamını Oku'"></button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="mt-2 flex items-center gap-3 text-xs opacity-60 {{ $textClass }}">
                            @if($announcement->creator)
                                <span>👤 {{ $announcement->creator->name }}</span>
                            @else
                                <span>👤 System</span>
                            @endif
                            @if($announcement->published_at)
                                <span>🕐 {{ $announcement->published_at->diffForHumans() }}</span>
                            @endif
                            @if($announcement->expires_at)
                                <span>⏳ Expires {{ $announcement->expires_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
