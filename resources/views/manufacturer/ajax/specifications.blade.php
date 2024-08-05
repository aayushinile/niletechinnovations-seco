@if ($specifications->isNotEmpty())
    @foreach ($specifications as $specification)
        <div class="col-md-4" id="specification-{{ $specification->id }}">
            <div class="specification-info">
                <div class="specification-content">
                    <div class="specificationcheckbox">
                        <input type="checkbox" name="specifications[]" id="{{ $specification->id }}"
                            value="{{ $specification->id }}">
                        <label for="{{ $specification->id }}">&nbsp</label>
                    </div>
                    @if (!empty($specification->image))
                        <div class="specification-info-icon">
                            <img src="{{ asset('upload/specification-image/' . $specification->image) }}">
                        </div>
                    @endif
                    <div class="specification-info-content">
                        <h2>{{ $specification->name }}</h2>
                        <p>{{ $specification->values }}</p>
                    </div>
                </div>
                <div class="specification-action">
                    <a class="editbtn1" href="#" data-bs-toggle="modal" data-bs-target="#editspecification"
                        onclick="openEditForm(this)" data-id="{{ $specification->id }}"
                        data-name="{{ $specification->name }}" data-values="{{ $specification->values }}"
                        data-image = "{{ $specification->image }}"><img src="{{ asset('images/edit-2.svg') }}"
                            style="margin-top:6px"></a>
                    <a class="trashbtn remove-specification" style="cursor: pointer"
                        onclick="deleteSpec({{ $specification->id }})"
                        data-specification-id="{{ $specification->id }}"><img src="{{ asset('images/trash.svg') }}"
                            style="margin-top:6px"></a>
                </div>
            </div>
        </div>
    @endforeach
@endif
