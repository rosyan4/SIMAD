<!-- Change Location Modal -->
<div class="modal fade" id="changeLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pindah Lokasi Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_location_id" class="form-label">Lokasi Baru</label>
                    <select class="form-select" id="new_location_id">
                        <option value="">Pilih Lokasi</option>
                        @foreach($locations as $location)
                        <option value="{{ $location->location_id }}" 
                                {{ $asset->location_id == $location->location_id ? 'selected' : '' }}>
                            {{ $location->name }} ({{ $location->type }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="location_notes" class="form-label">Alasan Pemindahan</label>
                    <textarea class="form-control" id="location_notes" rows="3" 
                              placeholder="Berikan alasan pemindahan lokasi..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" 
                        onclick="updateAssetField({{ $asset->asset_id }}, 'location_id', $('#new_location_id').val(), $('#location_notes').val())">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>