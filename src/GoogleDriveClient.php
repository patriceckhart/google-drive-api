<?php
namespace PatricEckhart;

class GoogleDriveClient
{

    /**
     * @var string
     */
    private $clientIdPathAndFileName;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $startingPoint;


    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param string $clientIdPathAndFileName
     * @param string $redirectUri
     * @param string $refreshToken
     * @param string $scope
     * @param string $startingPoint
     * @param int $pageSize
     */
    public function __construct(string $clientIdPathAndFileName, string $redirectUri, string $refreshToken, string $scope, string $startingPoint, int $pageSize = 10)
    {
        $this->clientIdPathAndFileName = $clientIdPathAndFileName;
        $this->redirectUri = $redirectUri;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
        $this->startingPoint = $startingPoint;
        $this->pageSize = $pageSize;
    }

    /**
     * @return array
     */
    public function list():array
    {
        $client = $this->getClient();
        $service = new \Google_Service_Drive($client);
        $optParams = array(
            'pageSize' => $this->pageSize,
            'fields' => 'nextPageToken, files(id, name, mimeType, kind, parents, webViewLink, webContentLink)'
        );
        $results = $service->files->listFiles($optParams);
        if (count($results->getFiles()) == 0) {
            return [];
        } else {
            $result = [];
            foreach ($results->getFiles() as $file) {
                $result[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'kind' => $file->getKind(),
                    'parent' => $file->getParents() !== null ? $file->getParents()[0] : false,
                    'webViewLink' => $file->getWebViewLink(),
                    'webContentLink' => $file->getWebContentLink(),
                    'publicFile' => str_replace('&export=download', '&export=stream', $file->getWebContentLink()),
                ];
            }
            return $this->buildTree($result);
        }
    }

    /**
     * @param array $elements
     * @param string|null $parentId
     * @return array
     */
    private function buildTree(array $elements, string $parentId = null):array
    {
        if($parentId === null) {
            $parentId = $this->startingPoint;
        }
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    /**
     * @return \Google_Client
     */
    private function getClient():\Google_Client
    {

        $client = new \Google_Client();
        $client->setAuthConfig($this->clientIdPathAndFileName);
        $client->addScope($this->scope);
        $client->setRedirectUri($this->redirectUri);
        $client->setAccessType('offline');
        $client->setApprovalPrompt("consent");
        $client->setIncludeGrantedScopes(true);
        $client->authorize();
        $client->fetchAccessTokenWithRefreshToken($this->refreshToken);
        return $client;
    }


}
