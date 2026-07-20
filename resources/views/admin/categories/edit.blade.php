<x-app-layout>
    <div class="container py-5">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                <h1 class="h2 h1-md display-5 fw-bold text-dark-blue">{{ __('messages.edit') }} {{ __('messages.categories') }}</h1>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ $category->slug }}" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="icon" class="form-label">Icono (Font Awesome)</label>
                    <input type="text" name="icon" id="icon" class="form-control" value="{{ $category->icon }}" placeholder="fas fa-venus">
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="order" class="form-label">Orden</label>
                    <input type="number" name="order" id="order" class="form-control" value="{{ $category->order }}">
                </div>
                <div class="col-12 mb-3">
                    <label for="background_image" class="form-label">Imagen de Fondo</label>
                    @if($category->background_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $category->background_image) }}" alt="Imagen actual" style="max-width: 300px; max-height: 200px; object-fit: cover; border-radius: 0.5rem;" class="img-thumbnail">
                        </div>
                    @endif
                    <input type="file" name="background_image" id="background_image" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Imagen de fondo para la tarjeta de la categoría (máx. 2MB). Dejar vacío para mantener la imagen actual.</small>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
