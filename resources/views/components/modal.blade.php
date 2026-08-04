
<div class="modal fade" id="addEditModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="modalTitle">Add Entity</h4>
        </div>
        <div class="modal-body" id="addEditModalBody">
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal('addEditModal')">Close</button>
          <button type="button" class="btn btn-primary" id="saveButton" onclick="triggerSaveChanges('addEditModal')">Save changes</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

