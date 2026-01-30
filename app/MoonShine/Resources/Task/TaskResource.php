<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Task;

use App\Models\Status;
use App\Models\Task;
use App\MoonShine\Resources\Status\StatusResource;
use App\MoonShine\Resources\Task\Pages\TaskIndexPage;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Leeto\MoonShineKanBan\Resources\KanBanResource;
use MoonShine\Crud\Buttons\DeleteButton;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use Illuminate\Http\Request;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\Laravel\Pages\Crud\FormPage;
use Illuminate\Http\Response;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

#[Icon('view-columns')]
#[Group('Kanban', 'squares-2x2')]
#[Order(10)]
class TaskResource extends KanBanResource
{
    protected string $model = Task::class;

    protected string $title = 'Tasks';

    protected string $column = 'title';

    protected string $sortColumn = 'sorting';

    protected ?string $description = 'description';

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    /**
     * @return list<class-string<\MoonShine\Contracts\Core\PageContract>>
     */
    protected function pages(): array
    {
        return [
            TaskIndexPage::class,
            FormPage::class,
        ];
    }

    public function statuses(): Collection
    {
        return Status::query()
            ->orderBy('sorting')
            ->pluck('name', 'id');
    }

    public function foreignKey(): string
    {
        return 'status_id';
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder
            ->orderBy($this->foreignKey())
            ->orderBy($this->getSortColumn());
    }

    #[AsyncMethod]
    public function sort(Request $request): Response
    {
        $model = $this->getModel();
        $keyName = $model->getKeyName();
        $itemId = $request->input('id');
        $parentId = $request->input('parent');

        $item = $model->newModelQuery()->firstWhere($keyName, $itemId);

        $item?->update([
            $this->getSortColumn() => $request->integer('index'),
            $this->foreignKey() => $parentId,
        ]);

        if ($request->filled('data')) {
            $ids = $request->str('data')
                ->explode(',')
                ->values();

            foreach ($ids as $index => $id) {
                $query = $model->newModelQuery()->where($keyName, $id);

                if ($request->has('parent')) {
                    $query->where($this->foreignKey(), $parentId);
                }

                $query->update([
                    $this->getSortColumn() => (int) $index,
                ]);
            }
        }

        return response()->noContent();
    }

    /**
     * @return list<\MoonShine\Contracts\UI\FieldContract|\MoonShine\Contracts\UI\ComponentContract>
     */
    protected function formFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Title', 'title')->required(),
            Textarea::make('Description', 'description'),
            BelongsTo::make('Status', 'status', resource: StatusResource::class)->required(),
            Number::make('Sort', 'sorting')
                ->default(0)
                ->min(0),
        ];
    }

    public function getIndexButtons(): array
    {
        return [
            ActionButton::make(
                'Edit',
                fn ($item) => $this->getPageUrl(
                    $this->getFormPage(),
                    params: ['resourceItem' => $item->id]
                )
            )
                ->icon('pencil')
                ->showInDropdown(),

            DeleteButton::for(
                $this,
                componentName: $this->getListComponentName(),
                redirectAfterDelete: $this->getIndexPageUrl(),
                modalName: "kanban-{$this->getUriKey()}",
            ),
        ];
    }

    /**
     * @return string[]
     */
    protected function search(): array
    {
        return [
            'id',
            'title',
        ];
    }
}
