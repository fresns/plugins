@extends('PortalEditor::fresns')

@section('content')
    <div class="m-5">
        @foreach($platforms as $platform)
            <div class="d-flex justify-content-start mb-3">
                <div class="input-group">
                    <span class="input-group-text">{{ $platform['name'] }}</span>
                    @if ($langStatus)
                        @foreach($langMenus as $menu)
                            <a class="btn btn-outline-secondary" href="{{ route('portal-editor.edit', ['id' => $platform['id'], 'langTag' => $menu['langTag']]) }}">
                                {{ $menu['langName'] }}
                                @if ($menu['areaStatus'])
                                    ({{ $menu['areaName'] }})
                                @endif
                            </a>
                        @endforeach
                    @else
                        <a class="btn btn-outline-secondary" href="{{ route('portal-editor.edit', ['id' => $platform['id'], 'langTag' => $defaultLanguage]) }}">
                            {{ __('FsLang::panel.button_edit') }}
                        </a>
                    @endif
                </div>

                @if ($platform['id'] == 4)
                    <div class="input-group">
                        <span class="input-group-text">{{ $platform['name'] }}</span>
                        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('PortalEditor::editor.auto_update') }}: {{ $portalEditorAuto ? __('FsLang::panel.option_activate') : __('FsLang::panel.option_deactivate') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form action="{{ route('portal-editor.update.auto') }}" method="post">
                                    @csrf
                                    @method('put')
                                    <button class="dropdown-item" type="submit">{{ $portalEditorAuto ? __('FsLang::panel.button_deactivate') : __('FsLang::panel.button_activate') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <div class="input-group w-25">
                        <form action="{{ route('portal-editor.update.now') }}" method="post">
                            @csrf
                            @method('post')
                            <button class="btn btn-outline-primary" type="submit">{{ __('PortalEditor::editor.update_now') }}</button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection
