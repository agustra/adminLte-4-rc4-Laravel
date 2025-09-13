<style>
.media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
.media-item { position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; transition: all 0.3s; }
.media-item:hover { border-color: #007bff; box-shadow: 0 2px 8px rgba(0,123,255,0.2); }
.media-item.selected { border-color: #007bff; background-color: rgba(0,123,255,0.1); }
.media-item img { width: 100%; height: 120px; object-fit: cover; border-radius: 4px; }
.media-item .file-icon { font-size: 48px; color: #6c757d; height: 120px; display: flex; align-items: center; justify-content: center; }

.media-item-actions { background: rgba(0,0,0,0.7); border-radius: 4px; padding: 2px; }
.media-item-actions .btn { margin: 1px; }
.upload-area { border: 2px dashed #ddd; border-radius: 8px; padding: 40px; text-align: center; margin-bottom: 20px; transition: all 0.3s; }
.upload-area:hover, .upload-area.dragover { border-color: #007bff; background-color: rgba(0,123,255,0.05); }
.view-toggle { margin-bottom: 15px; }
.media-toolbar { display: flex; justify-content: between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.selected-info { background: #e3f2fd; padding: 10px; border-radius: 4px; margin-bottom: 15px; display: none; }
#contextMenu { 
    box-shadow: 0 2px 10px rgba(0,0,0,0.2); 
    border: 1px solid #ddd; 
    max-height: 400px;
    overflow-y: auto;
    min-width: 200px;
    max-width: 300px;
}
#contextMenu .dropdown-item:hover { background-color: #f8f9fa; }
#folderListCopy, #folderListMove, #folderListForFolder {
    max-height: 150px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    margin: 5px 0;
    background: #fff;
}
#folderListCopy::-webkit-scrollbar, 
#folderListMove::-webkit-scrollbar, 
#folderListForFolder::-webkit-scrollbar {
    width: 6px;
}
#folderListCopy::-webkit-scrollbar-thumb, 
#folderListMove::-webkit-scrollbar-thumb, 
#folderListForFolder::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
#folderListCopy .dropdown-item, 
#folderListMove .dropdown-item, 
#folderListForFolder .dropdown-item {
    padding: 8px 12px;
    font-size: 0.875rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dropdown-header {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    padding: 4px 12px;
}

/* Responsive context menu */
@media (max-width: 768px) {
    #contextMenu {
        max-height: 300px;
        min-width: 180px;
        max-width: 250px;
    }
    #folderListCopy, #folderListMove, #folderListForFolder {
        max-height: 120px;
    }
}
</style>