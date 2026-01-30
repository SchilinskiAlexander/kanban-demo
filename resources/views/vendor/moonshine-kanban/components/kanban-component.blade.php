@php
    $pageUri = request()->route('pageUri');
    $resourceUri = request()->route('resourceUri');
    $resolvedSortRoute = $sortRoute;

    if ($pageUri && $resourceUri) {
        $resolvedSortRoute = route('moonshine.method', [
            'pageUri' => $pageUri,
            'resourceUri' => $resourceUri,
            'method' => 'sort',
        ], false);
    }
@endphp

<div class="w-full overflow-hidden">
    <div
        class="w-full overflow-x-auto"
        style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;     overflow-x: scroll;"
        x-data="kanbanBoardScroll"
    >
        <div class="flex gap-4 pb-4 px-4 select-none items-start min-w-max">
            @foreach ($statuses as $key => $title)
                <x-moonshine-kanban::column
                    :title="$title"
                    :key="$key"
                    :items="$data[$key] ?? []"
                    :buttons="$buttons"
                    :sortRoute="$resolvedSortRoute"
                />
            @endforeach
        </div>
    </div>
</div>

<script>
    function kanbanBoardScroll() {
        return {
            init() {
                const container = this.$el;
                const edge = 120;
                const speed = 20;

                document.addEventListener('dragover', (e) => {
                    const rect = container.getBoundingClientRect();
                    const x = e.clientX;

                    if (x < rect.left + edge) {
                        container.scrollLeft -= speed;
                    } else if (x > rect.right - edge) {
                        container.scrollLeft += speed;
                    }
                });
            }
        }
    }

    function kbSortable(sortRoute) {
        return {
            init() {
                const scrollSpeed = 15;
                const edgeSize = 80;
                const container = this.$el;

                Sortable.create(container, {
                    group: {name: 'kanban-group'},
                    animation: 150,
                    draggable: '[data-id]',
                    handle: '.handle',
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    dataIdAttr: 'data-id',

                    onStart(evt) {
                        evt.item.classList.add('kanban-lift');
                    },
                    onEnd(evt) {
                        evt.item.classList.remove('kanban-lift');
                    },

                    onMove(evt) {
                        const rect = container.getBoundingClientRect();
                        const y = evt.originalEvent.clientY;

                        if (y < rect.top + edgeSize) {
                            container.scrollTop -= scrollSpeed;
                        } else if (y > rect.bottom - edgeSize) {
                            container.scrollTop += scrollSpeed;
                        }
                    },

                    async onEnd(evt) {
                        const container = evt.to;
                        const parentSource = container.closest('[data-parent_key]') || container;
                        const itemSource = evt.item.closest('[data-id]') || evt.item;
                        const itemId = itemSource.getAttribute('data-id') || itemSource.dataset.id;
                        const parentKey = parentSource.getAttribute('data-parent_key')
                            || parentSource.dataset.parent_key
                            || parentSource.dataset.parentKey;
                        const ids = Array.from(container.querySelectorAll('[data-id]'))
                            .map((el) => el.getAttribute('data-id'))
                            .join(',');

                        let formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('id', itemId || '');
                        formData.append('parent', parentKey || '');
                        formData.append('index', evt.newIndex);
                        formData.append('data', ids);

                        await fetch(sortRoute, {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                    }
                });
            }
        }
    }
</script>
