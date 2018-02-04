<?php 

namespace WordPress\Plugins;

use DI\ContainerBuilder;

function wp_plugin_template_bootstrap() {
  $container_builder = new ContainerBuilder();
  $container = $container_builder->build();
  $plugin = $container->get('WordPress\Plugins\Plugin\Plugin');
  $plugin->init();
}

wp_plugin_template_bootstrap();