<div id="_youhonest_center_container">
    <div id="_youhonest_network_disable"
         data-network-id="<?=$network->getNetworkId()?>">
    </div>
    <h1> <?=$this->t('LOGIN::AUTHORIZATION')?> </h1>
    <div class="_youhonest_desription_wrap">
        <div class="_youhonest_description">
            <?=$this->t('LOGIN::LOGIN_DESCRIPTION')?>
        </div>
        <a id="_youhonest_network_big_link"
           href="<?=$network->getAuthorizationURL()?>">
            <div class="_youhonest_network_link_class_<?=$network->getNetworkId()?>"></div>
            <?=$this->t('LOGIN::AUTHORIZE')?>
        </a>
    </div>
    <div class="_youhonest_auth_content">
        <p>
             <span> 
                 <?=$this->t('LOGIN::YOU_CAN_DISABLE', array(
                     'NAME' => $network->getNetworkName(),
                 ))?>
             </span>
             <span>
                 <?=$this->t('LOGIN::YOU_CAN_ENABLE', array(
                     'NAME' => $network->getNetworkName(),
                 ))?>
             </span>
        </p>
        <p>
             <span>
                 <?=$this->t('LOGIN::WHAT_IS_THIS', array(
                     'LINK' => '<a href="' . $this->externalUrl . '/about" target="_blank">' .
                               $this->t('LOGIN::ABOUT_PAGE') .
                               '</a>',
                 ))?>
             </span>
             <span>
                 <?=$this->t('LOGIN::AGREE_TERMS', array(
                     'LINK' => '<a href="' . $this->externalUrl . '/terms" target="_blank">' .
                               $this->t('LOGIN::TERMS') .
                               '</a>',
                 ))?>
             </span>
        </p>
    </div>
</div>