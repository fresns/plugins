@extends('SharePoster::layout')

@section('content')
    <form action="{{ route('share-poster.admin.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')

        <input type="hidden" name="type" value="post">

        {{-- background --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.background') }}:</label>
            <div class="col-lg-4"><input type="file" class="form-control" name="background_file" accept="image/png,image/jpeg,image/bmp,.png,.jpg,.jpeg,.bmp"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> <a href="{{ $background_url }}" target="_blank">{{ __('FsLang::panel.button_view') }}</a></div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.cache') }}:</label>
            <div class="col-lg-4 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cache" id="cache_yes" value="1" @if(($config['cache'] ?? false)) checked @endif>
                    <label class="form-check-label" for="cache_yes">{{ __('FsLang::panel.option_open') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cache" id="cache_no" value="0" @if(! ($config['cache'] ?? false)) checked @endif>
                    <label class="form-check-label" for="cache_no">{{ __('FsLang::panel.option_close') }}</label>
                </div>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_cache') }}</div>
        </div>

        <div class="text-success"><hr></div>

        {{-- author avatar --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_avatar_size') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="avatar_size" value="{{ $config['avatar_size'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_avatar_size') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_avatar_circle') }}:</label>
            <div class="col-lg-4 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="avatar_circle" id="avatar_circle_yes" value="1" @if(($config['avatar_circle'] ?? false)) checked @endif>
                    <label class="form-check-label" for="avatar_circle_yes">{{ __('SharePoster::fresns.option_circle') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="avatar_circle" id="avatar_circle_no" value="0" @if(! ($config['avatar_circle'] ?? false)) checked @endif>
                    <label class="form-check-label" for="avatar_circle_no">{{ __('SharePoster::fresns.option_square') }}</label>
                </div>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_avatar_circle') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_avatar_x_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="avatar_x_position" value="{{ $config['avatar_x_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> x - abscissa</div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_avatar_y_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="avatar_y_position" value="{{ $config['avatar_y_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> y - ordinate</div>
        </div>

        <div class="text-success"><hr></div>

        {{-- author nickname --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_nickname_color') }}:</label>
            <div class="col-lg-4"><input type="color" class="form-control form-control-color" name="nickname_color" value="{{ $config['nickname_color'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> #414141</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_nickname_font_size') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="nickname_font_size" value="{{ $config['nickname_font_size'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 62</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_nickname_x_center') }}:</label>
            <div class="col-lg-4 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="nickname_x_center" id="nickname_x_center_yes" value="1" @if(($config['nickname_x_center'] ?? false)) checked @endif>
                    <label class="form-check-label" for="nickname_x_center_yes">{{ __('FsLang::panel.option_yes') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="nickname_x_center" id="nickname_x_center_no" value="0" @if(! ($config['nickname_x_center'] ?? false)) checked @endif>
                    <label class="form-check-label" for="nickname_x_center_no">{{ __('FsLang::panel.option_no') }}</label>
                </div>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_name_x_center') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_nickname_x_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="nickname_x_position" value="{{ $config['nickname_x_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> x - abscissa</div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_nickname_y_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="nickname_y_position" value="{{ $config['nickname_y_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> y - ordinate</div>
        </div>

        <div class="text-success"><hr></div>

        {{-- author bio --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_color') }}:</label>
            <div class="col-lg-4"><input type="color" class="form-control form-control-color" name="bio_color" value="{{ $config['bio_color'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> #7c7c7c</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_font_size') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="bio_font_size" value="{{ $config['bio_font_size'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 44</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_x_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="bio_x_position" value="{{ $config['bio_x_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> x - abscissa</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_y_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="bio_y_position" value="{{ $config['bio_y_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> y - ordinate</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_max_width') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="bio_max_width" value="{{ $config['bio_max_width'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_max_width') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_max_lines') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="bio_max_lines" value="{{ $config['bio_max_lines'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_max_lines') }}</div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.author_bio_line_spacing') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="bio_line_spacing" value="{{ $config['bio_line_spacing'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_line_spacing') }}</div>
        </div>

        <div class="text-success"><hr></div>

        {{-- title --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_color') }}:</label>
            <div class="col-lg-4"><input type="color" class="form-control form-control-color" name="title_color" value="{{ $config['title_color'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> #7c7c7c</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_font_size') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="title_font_size" value="{{ $config['title_font_size'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 44</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_x_center') }}:</label>
            <div class="col-lg-4 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="title_x_center" id="title_x_center_yes" value="1" @if(($config['title_x_center'] ?? false)) checked @endif>
                    <label class="form-check-label" for="title_x_center_yes">{{ __('FsLang::panel.option_yes') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="title_x_center" id="title_x_center_no" value="0" @if(! ($config['title_x_center'] ?? false)) checked @endif>
                    <label class="form-check-label" for="title_x_center_no">{{ __('FsLang::panel.option_no') }}</label>
                </div>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_name_x_center') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_x_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="title_x_position" value="{{ $config['title_x_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> x - abscissa</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_y_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="title_y_position" value="{{ $config['title_y_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> y - ordinate</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_max_width') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="title_max_width" value="{{ $config['title_max_width'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_max_width') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_max_lines') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="title_max_lines" value="{{ $config['title_max_lines'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_max_lines') }}</div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.title_line_spacing') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="title_line_spacing" value="{{ $config['title_line_spacing'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_line_spacing') }}</div>
        </div>

        <div class="text-success"><hr></div>

        {{-- content --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_color') }}:</label>
            <div class="col-lg-4"><input type="color" class="form-control form-control-color" name="content_color" value="{{ $config['content_color'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> #7c7c7c</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_font_size') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="content_font_size" value="{{ $config['content_font_size'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 44</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_x_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="content_x_position" value="{{ $config['content_x_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> x - abscissa</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_y_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="content_y_position" value="{{ $config['content_y_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> y - ordinate</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_max_width') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="content_max_width" value="{{ $config['content_max_width'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_max_width') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_max_lines') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="content_max_lines" value="{{ $config['content_max_lines'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_max_lines') }}</div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.content_line_spacing') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="content_line_spacing" value="{{ $config['content_line_spacing'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_line_spacing') }}</div>
        </div>

        <div class="text-success"><hr></div>

        {{-- qrcode --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.qrcode_size') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="qrcode_size" value="{{ $config['qrcode_size'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_qrcode_size') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.qrcode_x_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="qrcode_x_position" value="{{ $config['qrcode_x_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> x - abscissa</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.qrcode_y_position') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="qrcode_y_position" value="{{ $config['qrcode_y_position'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> y - ordinate</div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.qrcode_bottom_margin') }}:</label>
            <div class="col-lg-4"><input type="number" class="form-control" name="qrcode_bottom_margin" value="{{ $config['qrcode_bottom_margin'] ?? '' }}"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_qrcode_bottom_margin') }}</div>
        </div>

        <div class="text-success"><hr></div>

        <!-- button save -->
        <div class="row mb-4">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
            </div>
        </div>
    </form>
@endsection
