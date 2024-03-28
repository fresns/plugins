@extends('TitleIcons::commons.layout')

@use('App\Helpers\FileHelper')

@section('content')
    <div class="m-3">
        @if ($type == 'post')
            <a class="btn btn-danger btn-sm px-4" href="{{ route('title-icons.edit.post.title.icon', [
                'pid' => $model->pid,
                'langTag' => $langTag,
                'authUlid' => $authUlid,
            ]) }}" role="button">{{ $fsLang['remove'] }}</a>
        @else
            <a class="btn btn-danger btn-sm px-4" href="{{ route('title-icons.edit.comment.title.icon', [
                'cid' => $model->cid,
                'langTag' => $langTag,
                'authUlid' => $authUlid,
            ]) }}" role="button">{{ $fsLang['remove'] }}</a>
        @endif
    </div>

    <table class="table table-hover align-middle text-nowrap">
        <thead>
            <tr class="table-info">
                <th scope="col">{{ $fsLang['editorTitle'] }}</th>
                <th scope="col">{{ $fsLang['learnMore'] }}</th>
                <th scope="col">{{ $fsLang['image'] }}</th>
                <th scope="col">{{ $fsLang['select'] }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($titles as $title)
                <tr>
                    <td>{{ $title->getLangContent('name', $defaultLanguage) }}</td>
                    <td>{{ $title->getLangContent('description', $defaultLanguage) }}</td>
                    <td>
                        @php
                            $imageUrl = FileHelper::fresnsFileUrlByTableColumn($title->image_file_id, $title->image_file_url);
                        @endphp
                        <img src="{{ $imageUrl }}" style="max-height: 40px">
                    </td>
                    <td>
                        @if ($type == 'post')
                            <a class="btn btn-primary btn-sm" href="{{ route('title-icons.edit.post.title.icon', [
                                'pid' => $model->pid,
                                'operationId' => $title->id,
                                'langTag' => $langTag,
                                'authUlid' => $authUlid,
                            ]) }}" role="button">{{ $fsLang['setting'] }}</a>
                        @else
                            <a class="btn btn-primary btn-sm" href="{{ route('title-icons.edit.comment.title.icon', [
                                'cid' => $model->cid,
                                'operationId' => $title->id,
                                'langTag' => $langTag,
                                'authUlid' => $authUlid,
                            ]) }}" role="button">{{ $fsLang['setting'] }}</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
