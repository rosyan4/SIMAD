<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadDocumentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document" class="form-label">File Dokumen *</label>
                        <input type="file" class="form-control" id="document" name="document" 
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required>
                        <small class="text-muted">Maksimal 5MB. Format: PDF, JPG, PNG, DOC, XLS</small>
                    </div>
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Jenis Dokumen *</label>
                        <select class="form-select" id="document_type" name="document_type" required>
                            <option value="">Pilih Jenis</option>
                            <option value="pengadaan">Dokumen Pengadaan</option>
                            <option value="mutasi">Dokumen Mutasi</option>
                            <option value="penghapusan">Dokumen Penghapusan</option>
                            <option value="pemeliharaan">Dokumen Pemeliharaan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#uploadDocumentForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: `/opd/assets/{{ $asset->asset_id }}/upload-document`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Gagal upload dokumen');
            }
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Gagal upload dokumen');
        }
    });
});
</script>
@endpush