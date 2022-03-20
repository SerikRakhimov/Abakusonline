@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    ?>
    <p>
    @include('layouts.template.show_name',['template'=>$template])
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.relits')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location='{{route('relit.create', ['template'=>$template])}}'">
                    <i class="fas fa-plus d-inline"></i>
                    {{trans('main.add')}}
                </button>
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">Id</th>
            <th class="text-center">{{trans('main.serial_number')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.template')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $relits->firstItem() - 1;
        ?>
        @foreach($relits as $relit)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">
                    <a href="{{route('relit.show',$relit)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                <td class="text-center">
                    <a href="{{route('relit.show',$relit)}}" title="{{trans('main.show')}}">
                        {{$relit->id}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('relit.show',$relit)}}" title="{{trans('main.show')}}">
                        {{$relit->serial_number}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('relit.show',$relit)}}" title="{{trans('main.show')}}">
                        {{$relit->parent_template->name()}}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$relits->links()}}
@endsection

