<?php

namespace Vusalba\VueBundle\Service;


use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;

class Commande
{
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function setContainer(\Symfony\Component\DependencyInjection\Container $container) {
            $this->container = $container;
    }
    public function runCommand($commande, $arguments= array()) {
        try {
            var_dump('ici');
            $kernel = $this->container->get('kernel');
            $app = new Application($kernel);
            $args = array_merge(array('command' => $commande), $arguments);

            $input = new ArrayInput($args);
            $output = new NullOutput();

            $app->doRun($input, $output);
            var_dump('fin');
        }catch (\Exception $exception) {
            var_dump($exception);
        }

        return new Response("Command succesfully executed");
    }
}