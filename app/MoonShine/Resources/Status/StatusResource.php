<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Status;

use App\Models\Status;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

#[Icon('tag')]
#[Group('Kanban', 'squares-2x2')]
#[Order(11)]
class StatusResource extends ModelResource
{
    protected string $model = Status::class;

    protected string $title = 'Statuses';

    protected string $column = 'name';

    protected string $sortColumn = 'sorting';

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    /**
     * @return list<\MoonShine\Contracts\UI\FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', 'name')->sortable(),
            Number::make('Sort', 'sorting')->sortable(),
        ];
    }

    /**
     * @return list<\MoonShine\Contracts\UI\FieldContract|\MoonShine\Contracts\UI\ComponentContract>
     */
    protected function formFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', 'name')->required(),
            Number::make('Sort', 'sorting')
                ->default(0)
                ->min(0),
        ];
    }

    /**
     * @return string[]
     */
    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
