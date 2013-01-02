<? foreach ($networks as $network) { ?>
    <a href="<?=$network->getAuthorizationURL()?>"
       target="_blank"> <?=$network->getNetworkName()?>
    </a>
<? } ?>