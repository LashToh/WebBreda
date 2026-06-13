<?php

echo '<h1 class="page-header text-center">Packs Config</h1><hr>';
try {
    $creditSystem = new CreditSystem();
    # Coin
    if (check_value($_GET["delete_coin"])) {
        try {
            $newCfg = loadConfig("mp_packs_coin");
            if (!is_array($newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> MercadoPago Pack Coins is empty.");
            }
            if (!check_value($_GET["delete_coin"])) {
                throw new Exception("Invalid id.");
            }
            if (!array_key_exists($_GET["delete_coin"], $newCfg)) {
                throw new Exception("Invalid id.");
            }
            unset($newCfg[$_GET["delete_coin"]]);
            $mercadopagoJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__ . "mp_packs_coin.json", "w");
            if (!$cfgFile) {
                throw new Exception("<b>[Mercado Pago]</b> There was a problem opening the Mercado Pago Packs Coins file.");
            }
            fwrite($cfgFile, $mercadopagoJson);
            fclose($cfgFile);
            message("success", "<b>[Mercado Pago]</b> Changes successfully saved!");
        } catch (Exception $ex) {
            message("error", $ex->getMessage());
        }
    }
    if (check_value($_POST["mp_submit_coin"])) {
        try {
            $newCfg = loadConfig("mp_packs_coin");
            if (!is_array($newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> MercadoPago Pack Coins is empty.");
            }
            if (!check_value($_POST["mp_id_coin"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!check_value($_POST["mp_price_coin"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!check_value($_POST["mp_amount_coin"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            $elementId = $_POST["mp_id_coin"];
            $newElementData = array("active" => (bool) ($_POST["mp_status_coin"] == 1 ? true : false),
                                    "price" => $_POST["mp_price_coin"], 
                                    "amount" => $_POST["mp_amount_coin"], 
                                    "type_M" => $_POST["mp_type_coin"], 
                                    "order" => (int) $_POST["mp_order_coin"]);
            $newCfg[$elementId] = $newElementData;
            usort($newCfg, function ($a, $b) {
                return $a["order"] - $b["order"];
            });
            $mercadopagoJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__ . "mp_packs_coin.json", "w");
            if (!$cfgFile) {
                throw new Exception("<b>[Mercado Pago]</b> There was a problem opening the mercadopago file.");
            }
            fwrite($cfgFile, $mercadopagoJson);
            fclose($cfgFile);
            message("success", "<b>[Mercado Pago]</b> Changes successfully saved!");
        } catch (Exception $ex) {
            message("error", $ex->getMessage());
        }
    }
    if (check_value($_POST["mp_new_submit_coin"])) {
        try {
            $newCfg = loadConfig("mp_packs_coin");
            if (!is_array($newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> mercadopago configs empty.");
            }
            if (!check_value($_POST["mp_price_coin"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!check_value($_POST["mp_amount_coin"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            $newElementData = array("active" => (bool) ($_POST["mp_status_coin"] == 1 ? true : false), 
                                    "price" => $_POST["mp_price_coin"], 
                                    "amount" => $_POST["mp_amount_coin"], 
                                    "type_M" => $_POST["mp_type_coin"], 
                                    "order" => (int) $_POST["mp_order_coin"]);
            $newCfg[] = $newElementData;
            usort($newCfg, function ($a, $b) {
                return $a["order"] - $b["order"];
            });
            $mercadopagoJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__ . "mp_packs_coin.json", "w");
            if (!$cfgFile) {
                throw new Exception("<b>[Mercado Pago]</b> There was a problem opening the mercadopago file.");
            }
            fwrite($cfgFile, $mercadopagoJson);
            fclose($cfgFile);
            message("success", "<b>[Mercado Pago]</b> mercadopago successfully updated!");
        } catch (Exception $ex) {
            message("error", $ex->getMessage());
        }
    }
    $cfgCoin = loadConfig("mp_packs_coin");

    # VIP
    if (check_value($_GET["delete_vip"])) {
        try {
            $newCfg = loadConfig("mp_packs_vip");
            if (!is_array($newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> MercadoPago is empty.");
            }
            if (!check_value($_GET["delete_vip"])) {
                throw new Exception("<b>[Mercado Pago]</b> Invalid id.");
            }
            if (!array_key_exists($_GET["delete_vip"], $newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> Invalid id.");
            }
            unset($newCfg[$_GET["delete_vip"]]);
            $mercadopagoJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__ . "mp_packs_vip.json", "w");
            if (!$cfgFile) {
                throw new Exception("<b>[Mercado Pago]</b> There was a problem opening the mercadopago file.");
            }
            fwrite($cfgFile, $mercadopagoJson);
            fclose($cfgFile);
            message("success", "<b>[Mercado Pago]</b> Changes successfully saved!");
        } catch (Exception $ex) {
            message("error", $ex->getMessage());
        }
    }
    if (check_value($_POST["mp_submit_vip"])) {
        try {
            $newCfg = loadConfig("mp_packs_vip");
            if (!is_array($newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> MercadoPago esta vacio.");
            }
            if (!check_value($_POST["mp_id_vip"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!check_value($_POST["mp_price_vip"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!check_value($_POST["mp_amount_vip"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!in_array($_POST["mp_type_vip"], array("1", "2", "3"))) {
                throw new Exception("<b>[Mercado Pago]</b> Type VIP is not valid.");
            }
            $elementId = $_POST["mp_id_vip"];
            $newElementData = array("active" => (bool) ($_POST["mp_status_vip"] == 1 ? true : false), 
                                    "price" => $_POST["mp_price_vip"], 
                                    "amount" => $_POST["mp_amount_vip"], 
                                    "type_V" => $_POST["mp_type_vip"], 
                                    "order" => (int) $_POST["mp_order_vip"]);
            $newCfg[$elementId] = $newElementData;
            usort($newCfg, function ($a, $b) {
                return $a["order"] - $b["order"];
            });
            $mercadopagoJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__ . "mp_packs_vip.json", "w");
            if (!$cfgFile) {
                throw new Exception("<b>[Mercado Pago]</b> There was a problem opening the mercadopago file.");
            }
            fwrite($cfgFile, $mercadopagoJson);
            fclose($cfgFile);
            message("success", "<b>[Mercado Pago]</b> Changes successfully saved!");
        } catch (Exception $ex) {
            message("error", $ex->getMessage());
        }
    }
    if (check_value($_POST["mp_new_submit_vip"])) {
        try {
            $newCfg = loadConfig("mp_packs_vip");
            if (!is_array($newCfg)) {
                throw new Exception("<b>[Mercado Pago]</b> mercadopago configs empty.");
            }
            if (!check_value($_POST["mp_price_vip"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!check_value($_POST["mp_amount_vip"])) {
                throw new Exception("<b>[Mercado Pago]</b> Please fill all the form fields.");
            }
            if (!in_array($_POST["mp_type_vip"], array("1", "2", "3"))) {
                throw new Exception("<b>[Mercado Pago]</b> Type VIP is not valid.");
            }
            $newElementData = array("active" => (bool) ($_POST["mp_status_vip"] == 1 ? true : false), 
                                    "price" => $_POST["mp_price_vip"], 
                                    "amount" => $_POST["mp_amount_vip"], 
                                    "type_V" => $_POST["mp_type_vip"], 
                                    "order" => (int) $_POST["mp_order_vip"]);
            $newCfg[] = $newElementData;
            usort($newCfg, function ($a, $b) {
                return $a["order"] - $b["order"];
            });
            $mercadopagoJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__ . "mp_packs_vip.json", "w");
            if (!$cfgFile) {
                throw new Exception("<b>[Mercado Pago]</b> There was a problem opening the mercadopago file.");
            }
            fwrite($cfgFile, $mercadopagoJson);
            fclose($cfgFile);
            message("success", "<b>[Mercado Pago]</b> mercadopago successfully updated!");
        } catch (Exception $ex) {
            message("error", $ex->getMessage());
        }
    }
    $cfgVip = loadConfig("mp_packs_vip");

echo '<h3 class="page-header bg-primary text-white p-3 m-0"><i class="fab fa-bitcoin"></i> Coins Packs</h3>';
echo '<table class="table table-condensed table-bordered table-striped">';
    echo '<thead class="bg-dark text-center">';
        echo '<tr>';
            echo '<th class="text-white"></th>';
            echo '<th class="text-white">Order</th>';
            echo '<th class="text-white">Status</th>';
            echo '<th class="text-white">Price</th>';
            echo '<th class="text-white">Amout</th>';
            echo '<th class="text-white">Coins</th>';
            echo '<th class="text-white"></th>';
        echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($cfgCoin as $idCoin => $MercadopagoDataCoin) {
        echo '<form action="'.admincp_base('mercadopago_packs').'" method="post">';
            echo '<input type="hidden" name="mp_id_coin" value="' . $idCoin . '"/>';
            echo '<tr>';
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<a href="?module=mercadopago_packs&delete_coin=' . $idCoin . '" class="btn btn-danger btn-xs"><span class="fa fa-times" aria-hidden="true"></span></a>';
                echo '</td>';
                echo '<td style="max-width:70px;">';
                    echo '<input type="text" name="mp_order_coin" class="form-control" value="' . $MercadopagoDataCoin["order"] . '"/>';
                echo '</td>';
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<label class="radio-inline">';
                        echo '<input type="radio" name="mp_status_coin" value="1" ' . ($MercadopagoDataCoin["active"] ? "checked" : "") . '> Show';
                    echo '</label>';
                    echo '<label class="radio-inline">';
                        echo '<input type="radio" name="mp_status_coin" value="0" ' . (!$MercadopagoDataCoin["active"] ? "checked" : "") . '> Hide';
                    echo '</label>';
                echo '</td>';
                echo '<td>';
                    echo '<input type="text" name="mp_price_coin" class="form-control" value="' . $MercadopagoDataCoin["price"] . '"/>';
                echo '</td>';
                echo '<td>';
                    echo '<input type="text" name="mp_amount_coin" class="form-control" value="' . $MercadopagoDataCoin["amount"] . '"/>';
                echo '</td>';
                echo '<td>';
                    echo $creditSystem->buildSelectInput("mp_type_coin", $MercadopagoDataCoin["type_M"], "form-control");
                echo '</td>'; 
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<button type="submit" name="mp_submit_coin" value="ok" class="btn btn-primary">Save</button>';
                echo '</td>'; 
            echo '</tr>'; 
        echo '</form>';
    }
        echo '<form action="'.admincp_base('mercadopago_packs').'" method="post">'; 
            echo '<tr>'; 
                echo '<th colspan="9" class="text-center bg-success text-white text-center text-uppercase">Add New Coins Pack</th>';
            echo '</tr>'; 
            echo '<tr>'; 
                echo '<td class="text-center" style="vertical-align:middle;">'; 
                    echo '<a href="?module=mercadopago_packs&delete_coin=' . $idCoin . '" class="btn btn-danger btn-xs"><span class="fa fa-times" aria-hidden="true"></span></a>'; 
                echo '</td>';
                echo '<td style="max-width:70px;">';
                    echo '<input type="text" name="mp_order_coin" class="form-control"/>';
                echo '</td>'; 
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<label class="radio-inline">'; 
                        echo '<input type="radio" name="mp_status_coin" value="1" checked> Show';
                    echo '</label>'; 
                    echo '<label class="radio-inline">'; 
                        echo '<input type="radio" name="mp_status_coin" value="0"> Hide'; 
                    echo '</label>'; 
                echo '</td>'; 
                echo '<td>'; 
                    echo '<input type="text" name="mp_price_coin" class="form-control" />'; 
                echo '</td>'; 
                echo '<td>'; 
                    echo '<input type="text" name="mp_amount_coin" class="form-control" />'; 
                echo '</td>'; 
                echo '<td>';
                    echo $creditSystem->buildSelectInput("mp_type_coin", 0, "form-control");
                echo '</td>'; 
                echo '<td class="text-center" style="vertical-align:middle;">'; 
                    echo '<button type="submit" name="mp_new_submit_coin" value="ok" class="btn btn-success">Add</button>'; 
                echo '</td>'; 
            echo '</tr>'; 
        echo '</form>'; 
    echo '</tbody>'; 
echo '</table>';

echo '<hr><h3 class="page-header bg-primary text-white p-3 m-0"><i class="fas fa-star"></i> Vip Packs</h3>';

echo '<table class="table table-condensed table-bordered table-striped">';
    echo '<thead class="bg-dark text-center">';
        echo '<tr>';
            echo '<th class="text-white"></th>';
            echo '<th class="text-white">Order</th>';
            echo '<th class="text-white">Status</th>';
            echo '<th class="text-white">Price</th>';
            echo '<th class="text-white">Days</th>';
            echo '<th class="text-white">Level Vip</th>';
            echo '<th class="text-white"></th>';
        echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($cfgVip as $idVip => $MercadopagoDataVip) {
        echo '<form action="'.admincp_base('mercadopago_packs').'" method="post">';
            echo '<input type="hidden" name="mp_id_vip" value="' . $idVip . '"/>';
            echo '<tr>';
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<a href="?module=mercadopago_packs&delete_vip=' . $idVip . '" class="btn btn-danger btn-xs"><span class="fa fa-times" aria-hidden="true"></span></a>';
                echo '</td>';
                echo '<td style="max-width:70px;">';
                    echo '<input type="text" name="mp_order_vip" class="form-control" value="' . $MercadopagoDataVip["order"] . '"/>';
                echo '</td>';
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<label class="radio-inline">';
                        echo '<input type="radio" name="mp_status_vip" value="1" ' . ($MercadopagoDataVip["active"] ? "checked" : "") . '> Show';
                    echo '</label>';
                    echo '<label class="radio-inline">';
                        echo '<input type="radio" name="mp_status_vip" value="0" ' . (!$MercadopagoDataVip["active"] ? "checked" : "") . '> Hide';
                    echo '</label>';
                echo '</td>';
                echo '<td>';
                    echo '<input type="text" name="mp_price_vip" class="form-control" value="' . $MercadopagoDataVip["price"] . '"/>';
                echo '</td>';
                echo '<td>';
                    echo '<input type="text" name="mp_amount_vip" class="form-control" value="' . $MercadopagoDataVip["amount"] . '"/>';
                echo '</td>';
                echo '<td>';
                    echo '<select name="mp_type_vip" class="form-control">';
                        echo '<option value="1" ' . ($MercadopagoDataVip["type_V"] == "1" ? "selected" : "") . '>Bronze</option>';
                        echo '<option value="2" ' . ($MercadopagoDataVip["type_V"] == "2" ? "selected" : "") . '>Silver</option>';
                        echo '<option value="3" ' . ($MercadopagoDataVip["type_V"] == "3" ? "selected" : "") . '>Gold</option>';
                    echo '</select>';
                echo '</td>';
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<button type="submit" name="mp_submit_vip" value="ok" class="btn btn-primary">Save</button>';
                echo '</td>'; 
            echo '</tr>'; 
        echo '</form>';
    }
        echo '<form action="'.admincp_base('mercadopago_packs').'" method="post">'; 
            echo '<tr>'; 
                echo '<th colspan="9" class="text-center bg-success text-white text-center text-uppercase">Add New Vip Pack</th>';
            echo '</tr>'; 
            echo '<tr>'; 
                echo '<td class="text-center" style="vertical-align:middle;">'; 
                    echo '<a href="?module=mercadopago_packs&delete_vip=' . $idVip . '" class="btn btn-danger btn-xs"><span class="fa fa-times" aria-hidden="true"></span></a>'; 
                echo '</td>';
                echo '<td style="max-width:70px;">';
                    echo '<input type="text" name="mp_order_vip" class="form-control"/>';
                echo '</td>'; 
                echo '<td class="text-center" style="vertical-align:middle;">';
                    echo '<label class="radio-inline">'; 
                        echo '<input type="radio" name="mp_status_vip" value="1" checked> Show';
                    echo '</label>'; 
                    echo '<label class="radio-inline">'; 
                        echo '<input type="radio" name="mp_status_vip" value="0"> Hide'; 
                    echo '</label>'; 
                echo '</td>'; 
                echo '<td>'; 
                    echo '<input type="text" name="mp_price_vip" class="form-control" />'; 
                echo '</td>'; 
                echo '<td>'; 
                    echo '<input type="text" name="mp_amount_vip" class="form-control" />'; 
                echo '</td>';  
                echo '<td>'; 
                    echo '<select name="mp_type_vip" class="form-control">'; 
                        echo '<option value="1">Bronze</option>'; 
                        echo '<option value="2">Silver</option>'; 
                        echo '<option value="3">Gold</option>'; 
                    echo '</select>'; 
                echo '</td>';  
                echo '<td class="text-center" style="vertical-align:middle;">'; 
                    echo '<button type="submit" name="mp_new_submit_vip" value="ok" class="btn btn-success">Add</button>'; 
                echo '</td>'; 
            echo '</tr>'; 
        echo '</form>'; 
    echo '</tbody>'; 
echo '</table>';

} catch (Exception $ex) {
    message("error", $ex->getMessage());
}

?>