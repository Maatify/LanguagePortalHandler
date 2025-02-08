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
 * @Maatify   LanguagePortalHandler :: LanguageTable
 */

declare(strict_types=1);

namespace Maatify\LanguagePortalHandler\Tables;

use App\DB\DBS\DbPortalHandler;

class LanguageTable extends DbPortalHandler
{
    private static self $instance;

    // Singleton instance getter
    public static function obj(): self
    {
        return self::$instance ??= new self();
    }

    public const TABLE_NAME                 = 'language';
    public const TABLE_ALIAS                = self::TABLE_NAME;
    public const IDENTIFY_TABLE_ID_COL_NAME = 'language_id';
    public const LOGGER_TYPE                = self::TABLE_NAME;
    public const LOGGER_SUB_TYPE            = '';
    public const COLS                       = [
        self::IDENTIFY_TABLE_ID_COL_NAME => 1,
        'name'                           => 0,
        'short_name'                     => 0,
        'code'                           => 0,
        'locale'                         => 0,
        'image'                          => 0,
        'directory'                      => 0,
        'sort'                           => 1,
        'status'                         => 1,
    ];
    const        IMAGE_FOLDER               = self::TABLE_NAME;

    protected string $tableName = self::TABLE_NAME;
    protected string $tableAlias = self::TABLE_ALIAS;
    protected string $identify_table_id_col_name = self::IDENTIFY_TABLE_ID_COL_NAME;
    protected string $logger_type = self::LOGGER_TYPE;
    protected string $logger_sub_type = self::LOGGER_SUB_TYPE;
    protected array $cols = self::COLS;
    protected string $image_folder = self::IMAGE_FOLDER;
}