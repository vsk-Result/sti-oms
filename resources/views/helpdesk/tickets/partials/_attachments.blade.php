@if ($attachments->count() > 0)
    <div class="d-inline-flex align-items-center border border-dashed border-gray-300 rounded p-3 mt-5">
        @foreach($attachments as $attachment)
            <div class="d-flex flex-aligns-center pe-10">
                <img alt="" class="w-35px h-35px me-3" src="{{ get_extension_image_path($attachment->getExtensionAttribute()) }}">

                <div class="ms-1 fw-semibold">
                    <a target="_blank" href="{{ $attachment->getUrl() }}" class="fs-8 text-hover-primary fw-bold">{{ $attachment->file_name }}</a>

                    <div class="text-gray-400 fs-9">{{ $attachment->human_readable_size }}</div>
                </div>
            </div>
        @endforeach
    </div>
@endif