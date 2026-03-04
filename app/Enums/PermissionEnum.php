<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Backup Actions
    case VIEW_BACKUPS = 'view-backups';
    case CREATE_BACKUPS = 'create-backups';
    case DELETE_BACKUPS = 'delete-backups';
    case DOWNLOAD_BACKUPS = 'download-backups';
    case GENERATE_AI_SUMMARY = 'generate-ai-summary';

    // Vault Actions
    case VIEW_VAULTS = 'view-vaults';
    case CREATE_VAULTS = 'create-vaults';
    case DELETE_VAULTS = 'delete-vaults';
}
