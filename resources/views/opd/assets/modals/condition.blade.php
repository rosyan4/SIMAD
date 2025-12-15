<!-- Change Condition Modal -->
<div class="modal fade" id="changeConditionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Kondisi Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_condition" class="form-label">Kondisi Baru</label>
                    <select class="form-select" id="new_condition">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="condition_notes" class="form-label">Catatan</label>
                    <textarea class="form-control" id="condition_notes" rows="3" 
                              placeholder="Berikan catatan perubahan kondisi..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" 
                        onclick="updateAssetField({{ $asset->asset_id }}, 'condition', $('#new_condition').val(), $('#condition_notes').val())">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>