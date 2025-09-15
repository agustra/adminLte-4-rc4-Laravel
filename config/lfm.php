<?php

/*
|--------------------------------------------------------------------------
| Documentation for this config :
|--------------------------------------------------------------------------
| online  => http://unisharp.github.io/laravel-filemanager/config
| offline => vendor/unisharp/laravel-filemanager/docs/config.md
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
     */

    'use_package_routes'       => true,

    /*
    |--------------------------------------------------------------------------
    | Shared folder / Private folder
    |--------------------------------------------------------------------------
    |
    | If both options are set to false, then shared folder will be activated.
    |
     */

    'allow_private_folder'     => true,

    // Custom handler for admin/user folder access
    // Admin can see all folders, users only see their private folders
    'private_folder_name'      => App\Handler\UserFolderHandler::class,

    'allow_shared_folder'      => false, // Shared folder disabled for users, only admin can access via handler

    'shared_folder_name'       => 'public',

    /*
    |--------------------------------------------------------------------------
    | Folder Names
    |--------------------------------------------------------------------------
     */

    'folder_categories'        => [
        'file'  => [
            'folder_name'  => 'filemanager/files', // Separate from existing media
            'startup_view' => 'list',
            'max_size'     => 50000, // size in KB (50MB)
            'thumb' => true,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'valid_mime'   => [
                // Images
                'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp',
                
                // Documents
                'application/pdf',
                
                // Microsoft Office
                'application/msword', // .doc
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                'application/vnd.ms-excel', // .xls
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'application/vnd.ms-powerpoint', // .ppt
                'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
                
                // Text files
                'text/plain', 'text/csv', 'application/csv',
                'text/rtf', // Rich Text Format
                
                // Archives
                'application/zip', 'application/x-zip-compressed',
                'application/x-rar-compressed', // .rar
                'application/x-7z-compressed', // .7z
                
                // Other formats
                'application/json', // .json
                'application/xml', 'text/xml', // .xml
                'application/vnd.oasis.opendocument.text', // .odt (LibreOffice)
                'application/vnd.oasis.opendocument.spreadsheet', // .ods (LibreOffice)
            ],
        ],
        'image' => [
            'folder_name'  => 'filemanager/images', // Separate from existing media
            'startup_view' => 'grid',
            'max_size'     => 50000, // size in KB
            'thumb' => true,
            'thumb_width' => 150,
            'thumb_height' => 150,
            'valid_mime'   => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
     */

    'paginator' => [
        'perPage' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload / Validation
    |--------------------------------------------------------------------------
     */

    'disk'                     => 'public',

    'rename_file'              => false,

    'rename_duplicates'        => true,

    'alphanumeric_filename'    => false,

    'alphanumeric_directory'   => false,

    'should_validate_size'     => false,

    'should_validate_mime'     => true,

    // behavior on files with identical name
    // setting it to true cause old file replace with new one
    // setting it to false show `error-file-exist` error and stop upload
    'over_write_on_duplicate'  => true,

    // mimetypes of executables to prevent from uploading
    'disallowed_mimetypes' => ['text/x-php', 'text/html', 'text/plain'],

    // extensions of executables to prevent from uploading
    'disallowed_extensions' => ['php', 'html'],

    // Item Columns
    'item_columns' => ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url'],

    /*
    |--------------------------------------------------------------------------
    | Thumbnail
    |--------------------------------------------------------------------------
     */

    // If true, image thumbnails would be created during upload
    'should_create_thumbnails' => true,

    'thumb_folder_name'        => 'thumbs',

    // Create thumbnails automatically only for listed types.
    'raster_mimetypes'         => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
    ],

    'thumb_img_width'          => 300, // px

    'thumb_img_height'         => 300, // px

    /*
    |--------------------------------------------------------------------------
    | File Extension Information
    |--------------------------------------------------------------------------
     */

    'file_type_array'          => [
        // Documents
        'pdf'  => 'Adobe Acrobat',
        'doc'  => 'Microsoft Word',
        'docx' => 'Microsoft Word',
        'xls'  => 'Microsoft Excel',
        'xlsx' => 'Microsoft Excel',
        'ppt'  => 'Microsoft PowerPoint',
        'pptx' => 'Microsoft PowerPoint',
        'rtf'  => 'Rich Text Format',
        'odt'  => 'LibreOffice Writer',
        'ods'  => 'LibreOffice Calc',
        
        // Text & Data
        'txt'  => 'Text File',
        'csv'  => 'CSV File',
        'json' => 'JSON File',
        'xml'  => 'XML File',
        
        // Images
        'gif'  => 'GIF Image',
        'jpg'  => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png'  => 'PNG Image',
        'webp' => 'WebP Image',
        
        // Archives
        'zip'  => 'ZIP Archive',
        'rar'  => 'RAR Archive',
        '7z'   => '7-Zip Archive',
    ],

    /*
    |--------------------------------------------------------------------------
    | php.ini override
    |--------------------------------------------------------------------------
    |
    | These values override your php.ini settings before uploading files
    | Set these to false to ingnore and apply your php.ini settings
    |
    | Please note that the 'upload_max_filesize' & 'post_max_size'
    | directives are not supported.
     */
    'php_ini_overrides'        => [
        'memory_limit' => '256M',
        'upload_max_filesize' => '50M',
        'post_max_size' => '50M',
        'max_execution_time' => 300,
    ],

    'intervention_driver' => 'gd', // options: gd, imagick
];
