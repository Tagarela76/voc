<?php
/**
 * Authorize client by token for VOC WEB MANAGER
 */
class vwm_token
{
    public function login($userID, $token)
    {
        // SQL Authentication
        global $db, $xnyo_parent;

        // get any matching username
        $sql = "SELECT * FROM ".TB_USER." WHERE user_id = {$db->sqltext($userID)} " .
                " AND remote_auth_token = '{$token}'";
        echo $sql;
        // run query
        $db->exec($sql);

        // if no matches, invalid login
        if (!$db->num_rows())
            return false;

        // get stuffs
        $details = $db->fetch_array (0);

        // store data into the array
        if (XNYO_DEBUG) $xnyo_parent->trigger_error('Expanding group list (if found)');
        $details['groups'] = explode(',', $details['groups']);

        // remove any references to the password field
        unset($details['password']);
        return $details;
    }
}
