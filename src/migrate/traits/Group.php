<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CGroup;

/**
 * Трэйт с функциями для групп пользователей.
 */
trait Group
{
    /**
     * @var array
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function UserGroupCreate(array $data)
    {
        $return = [];
        if (empty($data['STRING_ID'])) {
            throw new Exception('You must set group STRING_ID');
        }
        if ($this->UserGetGroupIdByCode($data['STRING_ID'])) {
            throw new Exception('Group with STRING_ID ' . $data['STRING_ID'] . ' already exists');
        }
        $ib = new CGroup();
        $id = $ib->Add(array_merge(['ACTIVE' => 'Y'], $data));
        if ($id) {
            $return[] = "Add {$data['STRING_ID']} users group";
        } else {
            throw new Exception("Can't create {$data['STRING_ID']} users group");
        }

        return $return;
    }

    /**
     * @var string
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function UserGroupDelete($groupName)
    {
        $return = [];
        $id = $this->UserGetGroupIdByCode($groupName);
        if ($id) {
            $group = new CGroup();
            if ($group->Delete($id)) {
                $return[] = "Delete group {$groupName}";
            } else {
                throw new Exception("Can't delete group {$groupName}");
            }
        } else {
            throw new Exception("Group {$groupName} does not exist");
        }

        return $return;
    }

    /**
     * @var string
     *
     * @return int|null
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function UserGetGroupIdByCode($code)
    {
        $rsGroups = CGroup::GetList(($by = 'c_sort'), ($order = 'desc'), [
            'STRING_ID' => $code,
        ]);
        if ($ob = $rsGroups->Fetch()) {
            return $ob['ID'];
        } else {
            return null;
        }
    }
}
