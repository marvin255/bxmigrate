<?php echo "<?php\n"; ?>

/**
 * Миграция для создания инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\n"; ?>
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        return $this->IblockCreate(
            [
                'CODE' => '<?php echo mb_strtolower($smart_param_1); ?>',
                'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
                'SORT' => 500,
                'IBLOCK_TYPE_ID' => '', //insert your iblock type id
                'VERSION' => 2,
                'INDEX_SECTION' => 'Y',
                'INDEX_ELEMENT' => 'Y',
                'LIST_MODE' => 'S',
                'LIST_PAGE_URL' => '/<?php echo mb_strtolower($smart_param_1); ?>/',
                'SECTION_PAGE_URL' => '/<?php echo mb_strtolower($smart_param_1); ?>/#SECTION_CODE_PATH#/',
                'DETAIL_PAGE_URL' => '/<?php echo mb_strtolower($smart_param_1); ?>/#SECTION_CODE_PATH#/#CODE#/',
                'GROUP_ID' => [
                    1 => 'X',
                    2 => 'R',
                ],
            ],
            [
                'CODE' => [
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => [
                        'TRANSLITERATION' => 'Y',
                        'TRANS_LEN' => 100,
                        'TRANS_CASE' => 'L',
                        'TRANS_SPACE' => '-',
                        'TRANS_OTHER' => '-',
                        'TRANS_EAT' => 'Y',
                    ],
                ],
                'SECTION_CODE' => [
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => [
                        'TRANSLITERATION' => 'Y',
                        'TRANS_LEN' => 100,
                        'TRANS_CASE' => 'L',
                        'TRANS_SPACE' => '-',
                        'TRANS_OTHER' => '-',
                        'TRANS_EAT' => 'Y',
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return $this->IblockDelete('<?php echo mb_strtolower($smart_param_1); ?>');
    }
}
