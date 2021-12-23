@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Link;
    use App\Models\Main;
    ?>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>{{trans('main.items')}}</h3>
            </div>
            <div class="col-1 text-left">
                <a href="{{route('item.create')}}" title = "{{trans('main.add')}}">
                    <img src="{{Storage::url('add_record.png')}}" width="15" height="15" alt = "{{trans('main.add')}}">
                </a>
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-left">{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.name')}}</th>
            <th class="text-center">Id</th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $items->firstItem() - 1;
        ?>
        @foreach($items as $item)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">{{$i}}</td>
                <td class="text-left">
                    <a href="{{route('item.show',$item)}}">
                        {{$item->base->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('item.show',$item)}}">
                        {{$item->name()}}
                    </a>
                </td>
                <td class="text-center">
                    {{$item->id}}
                </td>
                <td class="text-left">
                    <?php
                    $link = Link::where('child_base_id', $item->base_id)->first();
                    $main = Main::where('child_item_id', $item->id)->first();
                    ?>
                    @if ($link != null)
                        @if ($main != null)
                                {{trans('main.full')}}
                        @else
                                <span class="text-danger font-weight-bold">{{trans('main.empty')}}</span>
                        @endif
                    @endif
                </td>
                <td class="text-left">
                    <?php
                    $link = Link::where('parent_base_id', $item->base_id)->first();
                    $main = Main::where('parent_item_id', $item->id)->first();
                    ?>
                    @if ($link != null)
                        @if ($main != null)
                                {{trans('main.used')}}
                        @else
                                {{trans('main.not_used')}}
                        @endif
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{route('item.ext_show', $item)}}" title="{{trans('main.view')}}">
                        <img src="{{Storage::url('view_record.png')}}" width="15" height="15"
                             alt="{{trans('main.view')}}">
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('main.index_item',$item)}}" title = "{{trans('main.information')}}">
                        <img src="{{Storage::url('info_record.png')}}" width="15" height="15" alt = "{{trans('main.info')}}">
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('item.ext_edit',$item)}}" title = "{{trans('main.edit')}}">
                        <img src="{{Storage::url('edit_record.png')}}" width="15" height="15" alt = "{{trans('main.edit')}}">
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('item.edit',$item)}}" title = "{{trans('main.edit')}}">
                        <img src="{{Storage::url('edit_record.png')}}" width="15" height="15" alt = "{{trans('main.edit')}}">
                    </a>
                </td>
                <td  class="text-center">
                    <a href="{{route('item.ext_delete_question',$item)}}" title = "{{trans('main.delete')}}">
                        <img src="{{Storage::url('delete_record.png')}}" width="15" height="15" alt = "{{trans('main.delete')}}">
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$items->links()}}
@endsection

