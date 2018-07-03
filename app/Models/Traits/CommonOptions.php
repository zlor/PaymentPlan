<?php

namespace App\Models\Traits;


trait CommonOptions
{
    /**
     * 性别 备选项
     * @return array
     */
    public static function getSexOptions()
    {
        return trans_options('sex', ['other', 'male', 'female']);
    }

    /**
     * 类别
     * @param String $lang
     * @param array $options
     *
     * @return array
     */
    public static function getTypeOptions($lang = 'lang', $options = [])
    {
        return trans_options('type', $options?$options:['department', 'work', 'task', 'other'], $lang);
    }

    /**
     * 状态
     * @param String $lang
     * @param array $options
     *
     * @return array
     */
    public static function getStatusOptions($lang='lang', $options = [])
    {
        return trans_options('status', $options?$options:['building', 'active', 'banned', 'close'], $lang);
    }

    /**
     * @param string $lang
     * @param array  $options
     * @param string $key
     *
     * @return array
     */
    public static function getL5Options($lang='lang', $options = [], $key='')
    {
        return trans_options($key, $options?$options:['building', 'active', 'banned', 'close'], $lang);
    }

    /**
     * 等级
     * @param String $lang
     * @param array $options
     *
     * @return array
     */
    public static function getLevelOptions($lang='lang', $options = [])
    {
        return trans_options('level', $options?$options:[0, 1], $lang);
    }

    /**
     * 布尔值选项
     *
     * @param string $lang
     * @param string $key
     *
     * @return array
     */
    public static function getBooleanOptions($lang = 'lang', $key = 'bool', $int = false)
    {
        return trans_options($key, $int?[0,1]:['false', 'true'], $lang);
    }


    /**
     * @return array
     */
    public static function getReportYearOptions()
    {
        $fromYear = 2017;

        $endYear = intval(date('Y'));

        $years = [];

        for($i = $fromYear; $i<=$endYear; $i++)
        {
            $years[$i] = $i.'年';
        }

        return $years;
    }
}
