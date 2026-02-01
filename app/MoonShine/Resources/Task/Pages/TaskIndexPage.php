<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Task\Pages;

use Leeto\MoonShineKanBan\View\Components\KanBanComponent;
use Illuminate\Http\Response;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Task\TaskResource;
use Leeto\MoonShineKanBan\DTOs\KanbanItem;
use MoonShine\Support\Attributes\AsyncMethod;

/**
 * @extends IndexPage<TaskResource>
 */
final class TaskIndexPage extends IndexPage
{
    protected function getItemsComponent(iterable $items, FieldsContract $fields): ComponentContract
    {
        $resource = $this->getResource();
        $description = $resource->getDescription();
        $items = collect($items)->map(static function ($item) use ($resource, $description) {
            $dto = KanbanItem::make(
                $item->id,
                $item->{$resource->getColumn()},
                $resource->foreignKey()
            )->setModel($item);

            if ($description) {
                $dto->setSubtitle($item->{$description});
            }

            return $dto;
        });

        return KanBanComponent::make(
            $resource,
            $items,
        );
    }

    #[AsyncMethod]
    public function sort(CrudRequestContract $request): Response
    {
        return $this->getResource()->sort($request);
    }
}
