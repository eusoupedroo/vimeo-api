<?php
namespace Phppot;

require __DIR__ . '/vendor/autoload.php';

use Vimeo\Vimeo;
use Vimeo\Exceptions\VimeoUploadException;

class VideoService
{
    private $vimeoClient;

    function __construct($clientId, $clientSecret, $accessToken)
    {
        try {
            $this->vimeoClient = new Vimeo($clientId, $clientSecret);
        } catch (\Exception $e) {
            die("Erro ao inicializar Vimeo Client: " . $e->getMessage());
        }

        try {
            $this->vimeoClient->setToken($accessToken);
            $accountInfo = $this->vimeoClient->request('/me');
            if (isset($accountInfo['body']['error'])) {
                die("Erro ao verificar accessToken: " . $accountInfo['body']['error']);
            }
        } catch (\Exception $e) {
            die("Erro ao verificar accessToken: " . $e->getMessage());
        }
    }

    function uploadVideo($videoFile)
    {
        try {
            if (!file_exists($videoFile)) {
                return array(
                    "type" => "error",
                    "error_message" => "O arquivo de vídeo não foi encontrado."
                );
            }

            // Verificar o tamanho do arquivo
            if (filesize($videoFile) == 0) {
                return array(
                    "type" => "error",
                    "error_message" => "O arquivo de vídeo está vazio."
                );
            }

            // Debug: Verificar se o arquivo de vídeo é legível
            if (!is_readable($videoFile)) {
                return array(
                    "type" => "error",
                    "error_message" => "O arquivo de vídeo não pode ser lido."
                );
            }

            // Iniciar o upload do vídeo
            $uri = $this->vimeoClient->upload($videoFile, array(
                'name' => 'Video Teste'
            ));

            // Debug: Verificar se o URI foi retornado
            if (!$uri) {
                return array(
                    "type" => "error",
                    "error_message" => "Erro ao iniciar o upload do vídeo: URI não retornado."
                );
            } 
            
            $videoData = $this->vimeoClient->request($uri);

            if ($videoData['status'] == 201) {
            $videoId = $videoData['body']['uri'];
            // Verifique se a URI está presente
            if (isset($videoId)) {
                $videoLink = "https://vimeo.com/manage" . $videoId;
                return array(
                    "type" => "success",
                    "link" => $videoLink
                );
            } else {
                return array(
                    "type" => "error",
                    "error_message" => "A URI do vídeo não foi encontrada."
                );
            }
            } else {
                return array(
                    "type" => "error",
                    "error_message" => "Failed to upload video",
                    "response" => $videoData
                );
            }
        } catch (VimeoUploadException $e) {
            return array(
                "type" => "error",
                "error_message" => $e->getMessage()
            );
        } catch (\Exception $e) {
            return array(
                "type" => "error",
                "error_message" => $e->getMessage()
            );
        }
    }

    private function getVideoLink($videoId)
    {
        $videoData = $this->vimeoClient->request($videoId);
        if (isset($videoData['body']['link'])) {
            return $videoData['body']['link'];
        } else {
            return null;
        }
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Example usage:
$config = array(
    'clientId' => 'c71ae120de0ce7fd4f74315780fcde8ab6bd2131',
    'clientSecret' => 'kGWARlpkok1eJPO+goYvK6+D8hxTdwUK2a+so9wr5YxRp7k8E+5RX0fMbT9vYFIvgzfOxgom41f7d7r7h5bRfBZAAsPHVWvfrWGulumASmrEMQf447eBPIobWd5S636D',
    'accessToken' => '48585f2ef83e7cf8efde490bbf30db36'
);

$videoService = new VideoService($config['clientId'], $config['clientSecret'], $config['accessToken']);

$videoFile = __DIR__ . '/videoteste.mp4'; 
$response = $videoService->uploadVideo($videoFile);

if ($response['type'] == "success") {
    echo "Upload realizado com sucesso. O vídeo pode ser acessado através da url: <a href='" . $response['link'] . "' target='_blank'>" . $response['link'] . "</a>";
} else if (isset($response['response']['body']['uri'])) {
    $uri = $response['response']['body']['uri'];
    echo "<button style='background: white; border: 1px solid black; padding: 8px;'><a href='https://vimeo.com/manage" . $uri . "' target='_blank' style='text-decoration: none; color: black; font-weight: bold; text-transform: uppercase;'>Upload Realizado, Acesse aqui</a></button>";
} else {
    echo "Erro ao realizar upload do vídeo: " . $response['error_message'];
    if (isset($response['response'])) {
        echo "<pre>";
        print_r($response['response']);
        echo "</pre>";
    }
}

?>