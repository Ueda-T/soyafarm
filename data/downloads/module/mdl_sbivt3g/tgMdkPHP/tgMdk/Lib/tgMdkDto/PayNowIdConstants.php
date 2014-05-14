<?php
/**
 * PayNowID用定義クラス
 *
 * @author kshimono
 */
class PayNowIdConstants {
    
    const SERVICE_TYPE_ACCOUNT   = "account";
    const SERVICE_TYPE_CHARGE    = "charge";
    const SERVICE_TYPE_RECURRING = "recurring";
    const SERVICE_TYPE_CARDINFO  = "cardinfo";
    
    const SERVICE_COMMAND_ADD     = "Add";
    const SERVICE_COMMAND_UPDATE  = "Update";
    const SERVICE_COMMAND_DELETE  = "Delete";
    const SERVICE_COMMAND_RESTORE = "Restore";
    const SERVICE_COMMAND_GET     = "Get";
}

?>
