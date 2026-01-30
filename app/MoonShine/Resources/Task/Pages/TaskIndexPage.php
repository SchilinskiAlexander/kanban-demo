<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Task\Pages;

use Leeto\MoonShineKanBan\View\Components\KanBanComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Task\TaskResource;
use MoonShine\Support\Attributes\AsyncMethod;

/**
 * @extends IndexPage<TaskResource>
 */
final class TaskIndexPage extends IndexPage
{
    protected function getItemsComponent(iterable $items, FieldsContract $fields): ComponentContract
    {
        return KanBanComponent::make(
            $this->getResource(),
            collect($items),
        );
    }

    #[AsyncMethod]
    public function sort(Request $request): Response
    {
        return $this->getResource()->sort($request);
    }
}
