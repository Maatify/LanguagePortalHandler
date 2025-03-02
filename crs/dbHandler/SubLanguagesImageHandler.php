<?php
/**
 * @PHP       Version >= 8.2
 * @Liberary  LanguagePortalHandler
 * @Project   LanguagePortalHandler
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2025-02-08 11:55 AM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/LanguagePortalHandler  view project on GitHub
 * @Maatify   LanguagePortalHandler :: SubLanguagesImageHandler
 */

namespace Maatify\LanguagePortalHandler\DBHandler;

use App\Assist\AppFunctions;
use JetBrains\PhpStorm\NoReturn;
use Maatify\Json\Json;
use Maatify\LanguagePortalHandler\Tables\LanguageTable;

abstract class SubLanguagesImageHandler extends SubClassLanguageHandler
{
    public const IMAGE_FOLDER = 'image';
    protected string $image_folder = self::IMAGE_FOLDER;

    public function RecordEmpty(int $language_id, int $cat_id): void
    {
        $this->Add([
            $this->identify_table_id_col_name         => $cat_id,
            LanguageTable::IDENTIFY_TABLE_ID_COL_NAME => $language_id,
            'agent_image'                             => 'image-not-found.png',
            'app_image'                               => 'image-not-found.png',
            'web_image'                               => 'image-not-found.png',
            'api_image'                               => 'image-not-found.png',
        ]);
    }

    #[NoReturn] public function UploadImageLanguage(): void
    {
        $this->row_id = $this->parent_class::obj()->ValidatePostedTableId();
        $parent_row = $this->parent_class::obj()->current_row;
        $this->current_row = $this->ValidateTableRowWithoutReplacement();
        $this->PostedLanguageId();
        $file =
            UploaderWebPPortalHandler::obj($this->image_folder)
                ->ImageUpload($this->row_id, $this->language_short_name . '-' . $parent_row['slug'], $this->current_row['image']);
        if (! empty($file['file'])) {
            $old_file = ! empty($file['deleted']) ? AppFunctions::SiteImageURL() . $file['deleted'] : 'NA';
            $new_file = AppFunctions::SiteImageURL() . /*$this->image_folder . '/' . */$file['file'];
            $log = $this->logger_keys =
                [
                    'language'                                => $this->language_short_name,
                    $this->identify_table_id_col_name         => $this->row_id,
                    LanguageTable::IDENTIFY_TABLE_ID_COL_NAME => $this->language_id,
                ];
            $log['image'] = ['from' => $old_file, 'to' => $new_file];
            $changes[] = [$log['image'], $old_file, $new_file];

            $this->Edit(
                [
                    'image' => /*$this->image_folder . '/' .*/ $file['file'],
                ],
                "`$this->identify_table_id_col_name` = ? AND `" . LanguageTable::IDENTIFY_TABLE_ID_COL_NAME . "` = ? ",
                [$this->row_id, $this->language_id]
            );
            $this->Logger($log, $changes, 'UploadImage');
            Json::Success(line: $this->class_name . __LINE__);
        } else {
            Json::TryAgain($this->class_name . __LINE__);
        }

    }

    #[NoReturn] public function UploadImageTypeLanguage(): void
    {
        $this->row_id = $this->parent_class::obj()->ValidatePostedTableId();
        $parent_row = $this->parent_class::obj()->current_row;
        $this->current_row = $this->ValidateTableRowWithoutReplacement();
        $this->PostedLanguageId();
        $file =
            UploaderWebPPortalHandler::obj($this->image_folder)
                ->ImageTypeUpload($this->language_short_name, $this->row_id, $parent_row['slug'], $this->current_row);
        if (! empty($file['file'])) {
            $old_file = ! empty($file['deleted']) ? AppFunctions::SiteImageURL() . $file['deleted'] : 'NA';
            $new_file = AppFunctions::SiteImageURL() . /*$this->image_folder . '/' .*/ $file['file'];
            $type = UploaderWebPPortalHandler::obj($this->image_folder)->GetType() . '_image';

            $log = $this->logger_keys =
                [
                    'language'                                => $this->language_short_name,
                    $this->identify_table_id_col_name         => $this->row_id,
                    LanguageTable::IDENTIFY_TABLE_ID_COL_NAME => $this->language_id,
                ];
            $log[$type] = ['from' => $old_file, 'to' => $new_file];
            $changes[] = [$type, $old_file, $new_file];

            $this->Edit(
                [
                    $type => /*$this->image_folder . '/' .*/ $file['file'],
                ],
                "`$this->identify_table_id_col_name` = ? AND `" . LanguageTable::IDENTIFY_TABLE_ID_COL_NAME . "` = ? ",
                [$this->row_id, $this->language_id]
            );
            $type = ucwords($type, '_');
            $type = str_replace('_', '', $type);
            $this->Logger($log, $changes, 'Upload' . ucfirst($type));
            Json::Success(line: $this->class_name . __LINE__);
        } else {
            Json::TryAgain($this->class_name . __LINE__);
        }
    }
}