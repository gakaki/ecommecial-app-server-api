<?php
$distirbution["name"] = "Taobao";
$distirbution["event"][0]["name"] = "subcontroler";
$distirbution["event"][0]["physical"] = "oauth.action.taobao.subControler()";
$distirbution["event"][0]["view"]["LOCATION"] = "oauth.view.taobao.location()";
$distirbution["event"][0]["view"]["OAUTH_ERROR"] = "oauth.view.taobao.oauth_error()";
$bunshop["module"][] = $distirbution;
