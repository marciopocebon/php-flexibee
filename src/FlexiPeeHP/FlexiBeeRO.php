<?php
/**
 * FlexiPeeHP - Read Only Access to FlexiBee class.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  (C) 2015-2019 Spoje.Net
 */

namespace FlexiPeeHP;

/**
 * Základní třída pro čtení z FlexiBee
 *
 * @url https://demo.flexibee.eu/devdoc/
 */
class FlexiBeeRO extends \Ease\Sand
{
    /**
     * Where to get JSON files with evidence stricture etc.
     * @var string
     */
    public static $infoDir = __DIR__.'/../../static';

    /**
     * Version of FlexiPeeHP library
     *
     * @var string
     */
    public static $libVersion = '1.20.3';

    /**
     * Základní namespace pro komunikaci s FlexiBee.
     * Basic namespace for communication with FlexiBee
     *
     * @var string Jmený prostor datového bloku odpovědi
     */
    public $nameSpace = 'winstrom';

    /**
     * URL of object data in FlexiBee
     * @var string url
     */
    public $apiURL = null;

    /**
     * Datový blok v poli odpovědi.
     * Data block in response field.
     *
     * @var string
     */
    public $resultField = 'results';

    /**
     * Verze protokolu použitého pro komunikaci.
     * Communication protocol version used.
     *
     * @var string Verze použitého API
     */
    public $protoVersion = '1.0';

    /**
     * Evidence užitá objektem.
     * Evidence used by object
     *
     * @link https://demo.flexibee.eu/c/demo/evidence-list Přehled evidencí
     * @var string
     */
    public $evidence = null;

    /**
     * Detaily evidence užité objektem
     * 
     * @var array 
     */
    public $evidenceInfo = [];

    /**
     * Výchozí formát pro komunikaci.
     * Default communication format.
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/format-types Přehled možných formátů
     *
     * @var string json|xml|...
     */
    public $format = 'json';

    /**
     * formát příchozí odpovědi
     * response format
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/format-types Přehled možných formátů
     *
     * @var string json|xml|...
     */
    public $responseFormat = 'json';

    /**
     * Curl Handle.
     *
     * @var resource
     */
    public $curl = null;

    /**
     * @link https://demo.flexibee.eu/devdoc/company-identifier Identifikátor firmy
     * @var string
     */
    public $company = null;

    /**
     * Server[:port]
     * @var string
     */
    public $url = null;

    /**
     * REST API Username
     * @var string
     */
    public $user = null;

    /**
     * REST API Password
     * @var string
     */
    public $password = null;

    /**
     * @var array Pole HTTP hlaviček odesílaných s každým požadavkem
     */
    public $defaultHttpHeaders = ['User-Agent' => 'FlexiPeeHP'];

    /**
     * Default additional request url parameters after question mark
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/urls   Common params
     * @link https://www.flexibee.eu/api/dokumentace/ref/paging Paging params
     * @var array
     */
    public $defaultUrlParams = ['limit' => 0];

    /**
     * Identifikační řetězec.
     *
     * @var string
     */
    public $init = null;

    /**
     * Sloupeček s názvem.
     *
     * @var string
     */
    public $nameColumn = 'nazev';

    /**
     * Sloupeček obsahující datum vložení záznamu do shopu.
     *
     * @var string
     */
    public $myCreateColumn = 'false';

    /**
     * Slopecek obsahujici datum poslení modifikace záznamu do shopu.
     *
     * @var string
     */
    public $myLastModifiedColumn = 'lastUpdate';

    /**
     * Klíčový idendifikátor záznamu.
     *
     * @var string
     */
    public $fbKeyColumn = 'id';

    /**
     * Informace o posledním HTTP requestu.
     *
     * @var *
     */
    public $curlInfo;

    /**
     * Informace o poslední HTTP chybě.
     *
     * @var string
     */
    public $lastCurlError = null;

    /**
     * Used codes storage.
     *
     * @var array
     */
    public $codes = null;

    /**
     * Last Inserted ID.
     *
     * @var int
     */
    public $lastInsertedID = null;

    /**
     * Default Line Prefix.
     *
     * @var string
     */
    public $prefix = '/c/';

    /**
     * Raw Content of last curl response
     *
     * @var string
     */
    public $lastCurlResponse;

    /**
     * HTTP Response code of last request
     *
     * @var int
     */
    public $lastResponseCode = null;

    /**
     * Body data  for next curl POST operation
     *
     * @var string
     */
    protected $postFields = null;

    /**
     * Last operation result data or message(s)
     *
     * @var array
     */
    public $lastResult = null;

    /**
     * Number from  @rowCount in response
     * @var int
     */
    public $rowCount = null;

    /**
     * Number from  @globalVersion
     * @var int
     */
    public $globalVersion = null;

    /**
     * @link https://www.flexibee.eu/api/dokumentace/ref/zamykani-odemykani/
     * @var string filter query
     */
    public $filter;

    /**
     * @link https://demo.flexibee.eu/devdoc/actions Provádění akcí
     * @var string
     */
    protected $action;

    /**
     * Pole akcí které podporuje ta která evidence
     * @link https://demo.flexibee.eu/c/demo/faktura-vydana/actions.json Např. Akce faktury
     * @var array
     */
    public $actionsAvailable = null;

    /**
     * Parmetry pro URL
     * @link https://www.flexibee.eu/api/dokumentace/ref/urls/ Všechny podporované parametry
     * @var array
     */
    public $urlParams = [
        'add-global-version',
        'add-row-count',
        'as-gui',
        'auth',
        'authSessionId',
        'code-as-id',
        'code-in-response',
        'delimeter',
        'detail', //See: https://www.flexibee.eu/api/dokumentace/ref/detail-levels
        'dir',
        'dry-run', // See: https://www.flexibee.eu/api/dokumentace/ref/dry-run/
        'encoding',
        'export-settings',
        'fail-on-warning',
        'filter',
        'format',
        'idUcetniObdobi',
        'includes',
        'inDesktopApp', // Note: Undocumented function (html only)
        'limit',
        'mode',
        'no-ext-ids',
        'no-http-errors',
        'no-ids',
        'only-ext-ids',
        'order',
        'relations',
        'report-lang',
        'report-name',
        'report-sign',
        'skupina-stitku',
        'sort',
        'start',
        'stitky-as-ids',
        'use-ext-id',
        'use-internal-id',
        'xpath', // See: https://www.flexibee.eu/api/dokumentace/ref/xpath/
    ];

    /**
     * Session ID
     * @var string
     */
    public $authSessionId = null;

    /**
     * Token obtained during login procedure
     * @var string 
     */
    public $refreshToken = null;

    /**
     * Save 404 results to log ?
     * @var boolean
     */
    protected $ignoreNotFound = false;

    /**
     * Array of errors caused by last request
     * @var array
     */
    private $errors = [];

    /**
     * List of Error500 reports sent
     * @var array
     */
    private $reports = [];

    /**
     * Send Error500 Report to
     * @var string email address
     */
    public $reportRecipient = 'podpora@flexibee.eu';

    /**
     * Formating string for \DateTime::format() for datetime columns
     * @var string
     */
    static public $DateTimeFormat = 'Y-m-d\TH:i:s.u+P';

    /**
     * Formating string for \DateTime::format() for date columns
     * @var string
     */
    static public $DateFormat = 'Y-m-d';

    /**
     * Last Request response stats
     * @var array 
     */
    private $responseStats = null;

    /**
     * Chained Objects
     * @var array
     */
    public $chained = [];

    /**
     * We Connect to server by default
     * @var boolean
     */
    public $offline = false;

    /**
     * Override cURL timeout
     * @var int seconds
     */
    public $timeout = null;

    /**
     * Columns Info for serveral evidencies
     * @var array 
     */
    private $columnsInfo = [];

    /**
     * Class for read only interaction with FlexiBee.
     *
     * @param mixed $init default record id or initial data
     * @param array $options Connection settings and other options override
     */
    public function __construct($init = null, $options = [])
    {
        $this->init = $init;

        parent::__construct();
        $this->setUp($options);
        $this->curlInit();
        if (!empty($init)) {
            $this->processInit($init);
        }
    }

    /**
     * Set internal Object name
     *
     * @param string $objectName
     *
     * @return string Jméno objektu
     */
    public function setObjectName($objectName = null)
    {
        return parent::setObjectName(is_null($objectName) ? ( empty($this->getRecordIdent())
                    ? $this->getObjectName() : $this->getRecordIdent().'@'.$this->getObjectName() )
                    : $objectName);
    }

    /**
     * SetUp Object to be ready for work
     *
     * @param array $options Object Options ( user,password,authSessionId
     *                                        company,url,evidence,
     *                                        prefix,defaultUrlParams,debug,
     *                                        detail,offline,filter,ignore404
     *                                        timeout
     */
    public function setUp($options = [])
    {
        $this->setupProperty($options, 'company', 'FLEXIBEE_COMPANY');
        $this->setupProperty($options, 'url', 'FLEXIBEE_URL');
        $this->setupProperty($options, 'user', 'FLEXIBEE_LOGIN');
        $this->setupProperty($options, 'password', 'FLEXIBEE_PASSWORD');
        $this->setupProperty($options, 'authSessionId', 'FLEXIBEE_AUTHSESSID');
        $this->setupProperty($options, 'timeout', 'FLEXIBEE_TIMEOUT');
        if (!empty($this->authSessionId)) {
            $this->defaultHttpHeaders['X-authSessionId'] = $this->authSessionId;
        }
        if (isset($options['evidence'])) {
            $this->setEvidence($options['evidence']);
        }
        $this->setupProperty($options, 'defaultUrlParams');
        if (isset($options['prefix'])) {
            $this->setPrefix($options['prefix']);
        }
        if (array_key_exists('detail', $options)) {
            $this->defaultUrlParams['detail'] = $options['detail'];
        }
        $this->setupProperty($options, 'filter');
        if (array_key_exists('offline', $options)) {
            $this->offline = (boolean) $options['offline'];
        }

        if (array_key_exists('ignore404', $options)) {
            $this->ignore404($options['ignore404']);
        }

        $this->setupProperty($options, 'debug');
        $this->updateApiURL();
    }

    /**
     * Set up one of properties
     *
     * @param array  $options  array of given properties
     * @param string $name     name of property to process
     * @param string $constant load default property value from constant
     */
    public function setupProperty($options, $name, $constant = null)
    {
        if (array_key_exists($name, $options)) {
            $this->$name = $options[$name];
        } else {
            if (property_exists($this, $name) && !empty($constant) && defined($constant)) {
                $this->$name = constant($constant);
            }
        }
    }

    /**
     * Get Current connection options for use in another object
     *
     * @return array usable as second constructor parameter
     */
    public function getConnectionOptions()
    {
        $conOpts = ['url' => $this->url];
        if (empty($this->authSessionId)) {
            $conOpts ['user']    = $this->user;
            $conOpts['password'] = $this->password;
        } else {
            $conOpts['authSessionId'] = $this->authSessionId;
        }
        $company = $this->getCompany();
        if (!empty($company)) {
            $conOpts['company'] = $company;
        }
        if (!is_null($this->timeout)) {
            $conOpts['timeout'] = $this->timeout;
        }
        return $conOpts;
    }

    /**
     * Inicializace CURL
     *
     * @return boolean Online Status
     */
    public function curlInit()
    {
        if ($this->offline === false) {
            $this->curl = \curl_init(); // create curl resource
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); // return content as a string from curl_exec
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true); // follow redirects (compatibility for future changes in FlexiBee)
            curl_setopt($this->curl, CURLOPT_HTTPAUTH, true);       // HTTP authentication
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false); // FlexiBee by default uses Self-Signed certificates
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_VERBOSE, ($this->debug === true)); // For debugging
            if (empty($this->authSessionId)) {
                curl_setopt($this->curl, CURLOPT_USERPWD,
                    $this->user.':'.$this->password); // set username and password
            }
            if (!is_null($this->timeout)) {
                curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
            }
        }
        return !$this->offline;
    }

    /**
     * Zinicializuje objekt dle daných dat. Možné hodnoty:
     *
     *  * 234                              - interní číslo záznamu k načtení
     *  * code:LOPATA                      - kód záznamu
     *  * BAGR                             - kód záznamu k načtení
     *  * ['id'=>24,'nazev'=>'hoblík']     - pole hodnot k předvyplnění
     *  * 743.json?relations=adresa,vazby  - část url s parametry k načtení
     *
     * @param mixed $init číslo/"(code:)kód"/(část)URI záznamu k načtení | pole hodnot k předvyplnění
     */
    public function processInit($init)
    {
        if (is_integer($init)) {
            $this->loadFromFlexiBee($init);
        } elseif (is_array($init)) {
            $this->takeData($init);
        } elseif (preg_match('/\.(json|xml|csv)/', $init)) {
            $this->takeData($this->getFlexiData((($init[0] != '/') ? $this->evidenceUrlWithSuffix($init)
                            : $init)));
        } else {
            $this->loadFromFlexiBee($init);
        }
    }

    /**
     * Set Data Field value
     *
     * @param string $columnName field name
     * @param mixed  $value      field data value
     *
     * @return bool Success
     */
    public function setDataValue($columnName, $value)
    {
        switch ($columnName) {
            case 'kod':
                $value  = self::uncode($value); //Alwyas uncode "kod" column
                break;
            default:
                if (is_object($value)) {
                    switch (get_class($value)) {
                        case 'DateTime':
                            $columnInfo = $this->getColumnInfo($columnName);
                            switch ($columnInfo['type']) {
                                case 'date':
                                    $value = self::dateToFlexiDate($value);
                                    break;
                                case 'datetime':
                                    $value = self::dateToFlexiDateTime($value);
                                    break;
                            }
                            break;
                    }
                }
                $result = parent::setDataValue($columnName, $value);
                break;
        }
        return $result;
    }

    /**
     * PHP Date object to FlexiBee date format
     * 
     * @param \DateTime $date
     */
    public static function dateToFlexiDate($date)
    {
        return $date->format(self::$DateFormat);
    }

    /**
     * PHP Date object to FlexiBee date format
     * 
     * @param \DateTime $dateTime
     */
    public static function dateToFlexiDateTime($dateTime)
    {
        return $dateTime->format(self::$DateTimeFormat);
    }

    /**
     * Set URL prefix
     *
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        switch ($prefix) {
            case 'a': //Access
            case 'c': //Company
            case 'u': //User
            case 'g': //License Groups
            case 'admin':
            case 'status':
            case 'login-logout':
                $this->prefix = '/'.$prefix.'/';
                break;
            case null:
            case '':
            case '/':
                $this->prefix = '';
                break;
            default:
                throw new \Exception(sprintf('Unknown prefix %s', $prefix));
        }
    }

    /**
     * Set communication format.
     * One of html|xml|json|csv|dbf|xls|isdoc|isdocx|edi|pdf|pdf|vcf|ical
     *
     * @param string $format
     * 
     * @return boolean format is availble
     */
    public function setFormat($format)
    {
        $result = true;
        if (($this->debug === true) && !empty($this->evidence) && isset(Formats::$$this->evidence)) {
            if (array_key_exists($format, array_flip(Formats::$$this->evidence))
                === false) {
                $result = false;
            }
        }
        if ($result === true) {
            $this->format = $format;
            $this->updateApiURL();
        }
        return $result;
    }

    /**
     * Nastaví Evidenci pro Komunikaci.
     * Set evidence for communication
     *
     * @param string $evidence evidence pathName to use
     * 
     * @return boolean evidence switching status
     */
    public function setEvidence($evidence)
    {
        switch ($this->prefix) {
            case '/c/':
                if ($this->debug === true) {
                    if (array_key_exists($evidence, EvidenceList::$name)) {
                        $this->evidence = $evidence;
                        $result         = true;
                    } else {
                        throw new \Exception(sprintf('Try to set unsupported evidence %s',
                                $evidence));
                    }
                } else {
                    $this->evidence = $evidence;
                    $result         = true;
                }
                break;
            default:
                $this->evidence = $evidence;
                $result         = true;
                break;
        }
        $this->updateApiURL();
        $this->evidenceInfo = $this->getEvidenceInfo();
        return $result;
    }

    /**
     * Vrací právě používanou evidenci pro komunikaci
     * Obtain current used evidence
     *
     * @return string
     */
    public function getEvidence()
    {
        return $this->evidence;
    }

    /**
     * Set used company.
     * Nastaví Firmu.
     *
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Obtain company now used
     * Vrací právě používanou firmu
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Vrací název evidence použité v odpovědích z FlexiBee
     *
     * @return string
     */
    public function getResponseEvidence()
    {
        switch ($this->evidence) {
            case 'c':
                $evidence = 'company';
                break;
            case 'evidence-list':
                $evidence = 'evidence';
                break;
            default:
                $evidence = $this->getEvidence();
                break;
        }
        return $evidence;
    }

    /**
     * Převede rekurzivně Objekt na pole.
     *
     * @param object|array $object
     *
     * @return array
     */
    public static function object2array($object)
    {
        $result = null;
        if (is_object($object)) {
            $objectData = get_object_vars($object);
            if (is_array($objectData) && count($objectData)) {
                $result = array_map('self::object2array', $objectData);
            }
        } else {
            if (is_array($object)) {
                foreach ($object as $item => $value) {
                    $result[$item] = self::object2array($value);
                }
            } else {
                $result = $object;
            }
        }

        return $result;
    }

    /**
     * Převede rekurzivně v poli všechny objekty na jejich identifikátory.
     *
     * @param object|array $object
     *
     * @return array
     */
    public static function objectToID($object)
    {
        $resultID = null;
        if (is_object($object) && method_exists($object, '__toString')
        ) {
            $resultID = $object->__toString();
        } else {
            if (is_array($object)) {
                foreach ($object as $item => $value) {
                    $resultID[$item] = self::objectToID($value);
                }
            } else { //String
                $resultID = $object;
            }
        }

        return $resultID;
    }

    /**
     * Return basic URL for used Evidence
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/urls/ Sestavování URL
     *
     * @return string Evidence URL
     */
    public function getEvidenceURL()
    {
        $evidenceUrl = $this->url.$this->prefix.$this->company;
        $evidence    = $this->getEvidence();
        if (!empty($evidence)) {
            $evidenceUrl .= '/'.$evidence;
        }
        return $evidenceUrl;
    }

    /**
     * Add suffix to Evidence URL
     *
     * @param string $urlSuffix
     *
     * @return string
     */
    public function evidenceUrlWithSuffix($urlSuffix)
    {
        $evidenceUrl = $this->getEvidenceUrl();
        if (!empty($urlSuffix)) {
            if (($urlSuffix[0] != '/') && ($urlSuffix[0] != ';') && ($urlSuffix[0]
                != '?')) {
                $evidenceUrl .= '/';
            }
            $evidenceUrl .= $urlSuffix;
        }
        return $evidenceUrl;
    }

    /**
     * Update $this->apiURL
     */
    public function updateApiURL()
    {
        $this->apiURL  = $this->getEvidenceURL();
        $rowIdentifier = $this->getRecordID();
        if (empty($rowIdentifier)) {
            $rowIdentifier = $this->getRecordCode();
            if (empty($rowIdentifier)) {
                $rowIdentifier = $this->getExternalID();
            }
        }
        if (!empty($rowIdentifier)) {
            $this->apiURL .= '/'.self::urlEncode($rowIdentifier);
        }
        $this->apiURL .= '.'.$this->format;
    }
    /*
     * Add Default Url params to given url if not overrided
     *
     * @param string $urlRaw
     *
     * @return string url with default params added
     */

    public function addDefaultUrlParams($urlRaw)
    {
        return \Ease\Shared::addUrlParams($urlRaw, $this->defaultUrlParams,
                false);
    }

    /**
     * Funkce, která provede I/O operaci a vyhodnotí výsledek.
     *
     * @param string $urlSuffix část URL za identifikátorem firmy.
     * @param string $method    HTTP/REST metoda
     * @param string $format    Requested format
     * 
     * @return array|boolean Výsledek operace
     */
    public function performRequest($urlSuffix = null, $method = 'GET',
                                   $format = null)
    {
        $this->rowCount      = null;
        $this->responseStats = [];

        if (preg_match('/^http/', $urlSuffix)) {
            $url = $urlSuffix;
        } elseif (strlen($urlSuffix) && ($urlSuffix[0] == '/')) {
            $url = $this->url.$urlSuffix;
        } else {
            $url = $this->evidenceUrlWithSuffix($urlSuffix);
        }

        $responseCode = $this->doCurlRequest($this->addDefaultUrlParams($url),
            $method, $format);

        return $this->parseResponse($this->rawResponseToArray($this->lastCurlResponse,
                    $this->responseFormat), $responseCode);
    }

    /**
     * Parse Raw FlexiBee response in several formats
     *
     * @param string $responseRaw raw response body
     * @param string $format      Raw Response format json|xml|etc
     *
     * @return array
     */
    public function rawResponseToArray($responseRaw, $format)
    {
        $responseDecoded = [];
        if (!empty(trim($responseRaw))) {
            switch ($format) {
                case 'json':
                    $responseDecoded = $this->rawJsonToArray($responseRaw);
                    break;
                case 'xml':
                    $responseDecoded = $this->rawXmlToArray($this->lastCurlResponse);
                    break;
                case 'txt':
                default:
                    $responseDecoded = [$this->lastCurlResponse];
                    break;
            }
        }
        return $responseDecoded;
    }

    /**
     * Convert FlexiBee Response JSON to Array
     *
     * @param string $rawJson
     *
     * @return array
     */
    public function rawJsonToArray($rawJson)
    {
        $responseDecoded = json_decode($rawJson, true, 10);
        $decodeError     = json_last_error_msg();
        if ($decodeError == 'No error') {
            if (array_key_exists($this->nameSpace, $responseDecoded)) {
                $responseDecoded = $responseDecoded[$this->nameSpace];
            }
        } else {
            $this->addStatusMessage('JSON Decoder: '.$decodeError, 'error');
            $this->addStatusMessage($rawJson, 'debug');
        }
        return $responseDecoded;
    }

    /**
     * Convert FlexiBee Response XML to Array
     *
     * @param string $rawXML
     *
     * @return array
     */
    public function rawXmlToArray($rawXML)
    {
        return self::xml2array($rawXML);
    }

    /**
     * Parse Response array
     *
     * @param array $responseDecoded
     * @param int $responseCode Request Response Code
     *
     * @return array main data part of response
     */
    public function parseResponse($responseDecoded, $responseCode)
    {
        if (is_array($responseDecoded)) {
            $mainResult          = $this->unifyResponseFormat($responseDecoded);
            $this->responseStats = array_key_exists('stats', $responseDecoded) ? (isset($responseDecoded['stats'][0])
                    ? $responseDecoded['stats'][0] : $responseDecoded['stats']) : null;
        } else {
            $mainResult = $responseDecoded;
        }
        switch ($responseCode) {
            case 201: //Success Write
            case 200: //Success Read
                if (is_array($responseDecoded)) {
                    $this->lastResult = $mainResult;
                    if (isset($responseDecoded['@rowCount'])) {
                        $this->rowCount = (int) $responseDecoded['@rowCount'];
                    }
                    if (isset($responseDecoded['@globalVersion'])) {
                        $this->globalVersion = (int) $responseDecoded['@globalVersion'];
                    }
                }
                break;

            case 500: // Internal Server Error
                if ($this->debug === true) {
                    $this->error500Reporter($responseDecoded);
                }
            case 404: // Page not found
                if ($this->ignoreNotFound === true) {
                    break;
                }
            case 400: //Bad Request parameters
            default: //Something goes wrong
                $this->addStatusMessage($this->lastResponseCode.': '.$this->curlInfo['url'],
                    'warning');
                if (is_array($responseDecoded)) {
                    $this->parseError($responseDecoded);
                }
                $this->logResult($responseDecoded, $this->curlInfo['url']);
                break;
        }
        return $mainResult;
    }

    /**
     * Parse error message response
     *
     * @param array $responseDecoded
     * 
     * @return int number of errors processed
     */
    public function parseError(array $responseDecoded)
    {
        if (array_key_exists('results', $responseDecoded)) {
            $this->errors = $responseDecoded['results'][0]['errors'];
            foreach ($this->errors as $errorInfo) {
                $this->addStatusMessage($errorInfo['message'], 'error');
                if (array_key_exists('for', $errorInfo)) {
                    unset($errorInfo['message']);
                    $this->addStatusMessage(json_encode($errorInfo), 'debug');
                }
            }
        } else {
            if (array_key_exists('message', $responseDecoded)) {
                $this->errors = [['message' => $responseDecoded['message']]];
            }
        }
        return count($this->errors);
    }

    /**
     * Vykonej HTTP požadavek
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/urls/ Sestavování URL
     * @param string $url    URL požadavku
     * @param string $method HTTP Method GET|POST|PUT|OPTIONS|DELETE
     * @param string $format požadovaný formát komunikace
     * 
     * @return int HTTP Response CODE
     */
    public function doCurlRequest($url, $method, $format = null)
    {
        if (is_null($format)) {
            $format = $this->format;
        }
        curl_setopt($this->curl, CURLOPT_URL, $url);
// Nastavení samotné operace
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
//Vždy nastavíme byť i prázná postdata jako ochranu před chybou 411
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postFields);

        $httpHeaders = $this->defaultHttpHeaders;

        $formats = Formats::bySuffix();

        if (!isset($httpHeaders['Accept'])) {
            $httpHeaders['Accept'] = $formats[$format]['content-type'];
        }
        if (!isset($httpHeaders['Content-Type'])) {
            $httpHeaders['Content-Type'] = $formats[$format]['content-type'];
        }
        $httpHeadersFinal = [];
        foreach ($httpHeaders as $key => $value) {
            if (($key == 'User-Agent') && ($value == 'FlexiPeeHP')) {
                $value .= ' v'.self::$libVersion;
            }
            $httpHeadersFinal[] = $key.': '.$value;
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpHeadersFinal);

// Proveď samotnou operaci
        $this->lastCurlResponse            = curl_exec($this->curl);
        $this->curlInfo                    = curl_getinfo($this->curl);
        $this->curlInfo['when']            = microtime();
        $this->curlInfo['request_headers'] = $httpHeadersFinal;
        $this->responseFormat              = $this->contentTypeToResponseFormat($this->curlInfo['content_type'],
            $url);
        $this->lastResponseCode            = $this->curlInfo['http_code'];
        $this->lastCurlError               = curl_error($this->curl);
        if (strlen($this->lastCurlError)) {
            $this->addStatusMessage(sprintf('Curl Error (HTTP %d): %s',
                    $this->lastResponseCode, $this->lastCurlError), 'error');
        }

        if ($this->debug === true) {
            $this->saveDebugFiles();
        }

        return $this->lastResponseCode;
    }

    /**
     * Obtain json for application/json
     * 
     * @param string $contentType
     * @param string $url
     * 
     * @return string response format
     */
    public function contentTypeToResponseFormat($contentType, $url = null)
    {
        if (!empty($url)) {
            $url = parse_url($url, PHP_URL_PATH);
        }

        $contentTypeClean = strstr($contentType, ';') ? substr($contentType, 0,
                strpos($contentType, ';')) : $contentType;

        switch ($url) {
            case '/login-logout/login';
                $responseFormat = 'json';
                break;
            default :
                switch ($contentTypeClean) {
                    case 'text/javascript':
                        $responseFormat = 'js';
                        break;

                    default:
                        $responseFormat = Formats::contentTypeToSuffix($contentTypeClean);
                        break;
                }
                break;
        }

        return $responseFormat;
    }

    /**
     * Nastaví druh prováděné akce.
     *
     * @link https://demo.flexibee.eu/devdoc/actions Provádění akcí
     * @param string $action
     * 
     * @return boolean
     */
    public function setAction($action)
    {
        $result           = false;
        $actionsAvailable = $this->getActionsInfo();
        if (is_array($actionsAvailable) && array_key_exists($action,
                $actionsAvailable)) {
            $this->action = $action;
            $result       = true;
        }
        return $result;
    }

    /**
     * Convert XML to array.
     *
     * @param string $xml
     *
     * @return array
     */
    public static function xml2array($xml)
    {
        $arr = [];
        if (!empty($xml)) {
            if (is_string($xml)) {
                $xml = simplexml_load_string($xml);
            }
            foreach ($xml->attributes() as $a) {
                $arr['@'.$a->getName()] = strval($a);
            }
            foreach ($xml->children() as $r) {
                if (count($r->children()) == 0) {
                    $arr[$r->getName()] = strval($r);
                } else {
                    $arr[$r->getName()][] = self::xml2array($r);
                }
            }
        }
        return $arr;
    }

    /**
     * Odpojení od FlexiBee.
     */
    public function disconnect()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        $this->curl = null;
    }

    /**
     * Disconnect CURL befere pass away
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Načte řádek dat z FlexiBee.
     *
     * @param int $recordID id požadovaného záznamu
     *
     * @return array
     */
    public function getFlexiRow($recordID)
    {
        $record   = null;
        $response = $this->performRequest($this->evidence.'/'.$recordID.'.json');
        if (isset($response[$this->evidence])) {
            $record = $response[$this->evidence][0];
        }

        return $record;
    }

    /**
     * Oddělí z pole podmínek ty jenž patří za ? v URL požadavku
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/urls/ Sestavování URL
     * @param array $conditions pole podmínek   - rendrují se do ()
     * @param array $urlParams  pole parametrů  - rendrují za ?
     */
    public function extractUrlParams(&$conditions, &$urlParams)
    {
        foreach ($this->urlParams as $urlParam) {
            if (isset($conditions[$urlParam])) {
                \Ease\Sand::divDataArray($conditions, $urlParams, $urlParam);
            }
        }
    }

    /**
     * convert unicode to entities for use with FlexiBee queries
     *
     * @param string $urlRaw
     * 
     * @return string
     */
    public static function urlEncode($urlRaw)
    {
        return str_replace(['%27', '%3A'], ["'", ':'], rawurlencode($urlRaw));
    }

    /**
     * Načte data z FlexiBee.
     *
     * @param string $suffix     dotaz
     * @param string|array       $conditions Custom filters or modifiers
     *
     * @return array Data obtained
     */
    public function getFlexiData($suffix = null, $conditions = null)
    {
        $finalUrl          = '';
        $evidenceToRestore = null;
        $urlParams         = $this->defaultUrlParams;

        if (!empty($conditions)) {
            if (is_array($conditions)) {
                $this->extractUrlParams($conditions, $urlParams);
                if (array_key_exists('evidence', $conditions) && is_null($this->getColumnInfo('evidence'))) {
                    $evidenceToRestore = $this->getEvidence();
                    $this->setEvidence($conditions['evidence']);
                    unset($conditions['evidence']);
                }
                $conditions = $this->flexiUrl($conditions);
            }

            if (strlen($conditions) && ($conditions[0] != '/')) {
                $conditions = '('.self::urlEncode($conditions).')';
            }
        }

        if (strlen($suffix)) {
            if (preg_match('/^http/', $suffix) || ($suffix[0] == '/') || is_numeric($suffix)) {
                $finalUrl = $suffix;
            } else {
                if (preg_match('/^(code|ext):(.*)/', $suffix)) {
                    $finalUrl = self::urlizeId($suffix);
                } else {
                    $finalUrl = $suffix;
                }
            }
        }

        $finalUrl .= $conditions;

        if (count($urlParams)) {
            if (strstr($finalUrl, '?')) {
                $finalUrl .= '&';
            } else {
                $finalUrl .= '?';
            }
            $finalUrl .= http_build_query($urlParams, null, '&',
                PHP_QUERY_RFC3986);
        }

        $transactions     = $this->performRequest($finalUrl, 'GET');
        $responseEvidence = $this->getResponseEvidence();
        if (is_array($transactions) && array_key_exists($responseEvidence,
                $transactions)) {
            $result = $transactions[$responseEvidence];
            if ((count($result) == 1) && (count(current($result)) == 0 )) {
                $result = null; // Response is empty Array
            }
        } else {
            $result = $transactions;
        }
        if (!is_null($evidenceToRestore)) {
            $this->setEvidence($evidenceToRestore);
        }
        return $result;
    }

    /**
     * Načte záznam z FlexiBee a uloží v sobě jeho data
     * Read FlexiBee record and store it inside od object
     *
     * @param int|string $id ID or conditions
     *
     * @return int počet načtených položek
     */
    public function loadFromFlexiBee($id = null)
    {
        $data = [];
        if (is_null($id)) {
            $id = $this->getMyKey();
        }
        $flexidata = $this->getFlexiData($this->getEvidenceUrl().'/'.self::urlizeId($id));
        if ($this->lastResponseCode == 200) {
            $this->apiURL = $this->curlInfo['url'];
            if (is_array($flexidata) && (count($flexidata) == 1) && is_array(current($flexidata))) {
                $data = current($flexidata);
            }
        }
        return $this->takeData($data);
    }

    /**
     * Reload current record from FlexiBee
     * 
     * @return boolean 
     */
    public function reload()
    {
        $id = $this->getRecordIdent();
        $this->dataReset();
        $this->loadFromFlexiBee($id);
        return $this->lastResponseCode == 200;
    }

    /**
     * Set Filter code for requests
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/zamykani-odemykani/
     *
     * @param array|string $filter filter formula or ['key'=>'value']
     *
     * @return string Filter code
     */
    public function setFilter($filter)
    {
        return $this->filter = is_array($filter) ? self::flexiUrl($filter) : $filter;
    }

    /**
     * Převede data do Json formátu pro FlexiBee.
     * Convert data to FlexiBee like Json format
     *
     * @url https://www.flexibee.eu/api/dokumentace/ref/actions/
     * @url https://www.flexibee.eu/api/dokumentace/ref/zamykani-odemykani/
     *
     * @param array $data    object data
     * @param int   $options json_encode options like JSON_PRETTY_PRINT etc
     *
     * @return string
     */
    public function getJsonizedData($data = null, $options = 0)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }

        $dataToJsonize = array_merge(['@version' => $this->protoVersion],
            $this->getDataForJSON($data));
        $jsonRaw       = json_encode([$this->nameSpace => $dataToJsonize],
            $options);

        return $jsonRaw;
    }

    /**
     * Get Data Fragment specific for current object
     *
     * @param array $data
     *
     * @return array
     */
    public function getDataForJSON($data = null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }

        $dataForJson = [$this->getEvidence() => $this->objectToID($data)];

        if (!is_null($this->action)) {
            $dataForJson[$this->evidence.'@action'] = $this->action;
            $this->action                           = null;
        }

        if (!is_null($this->filter)) {
            $dataForJson[$this->evidence.'@filter'] = $this->filter;
        }


        foreach ($this->chained as $chained) {
            $chainedData = $chained->getDataForJSON();
            foreach ($chainedData as $chainedItemEvidence => $chainedItemData) {
                if (array_key_exists($chainedItemEvidence, $dataForJson)) {
                    if (is_string(key($dataForJson[$chainedItemEvidence]))) {
                        $dataBackup                          = $dataForJson[$chainedItemEvidence];
                        $dataForJson[$chainedItemEvidence]   = [];
                        $dataForJson[$chainedItemEvidence][] = $dataBackup;
                    }
                    if (array_key_exists(0, $chainedItemData)) {
                        foreach ($chainedItemData as $chainedItem) {
                            $dataForJson[$chainedItemEvidence][] = $chainedItem;
                        }
                    } else {
                        $dataForJson[$chainedItemEvidence][] = $chainedItemData;
                    }
                } else {
                    $dataForJson[$chainedItemEvidence] = $chainedItemData;
                }
            }
        }


        return $dataForJson;
    }

    /**
     * Join another FlexiPeeHP Object
     *
     * @param FlexiBeeRO $object
     *
     * @return boolean adding to stack success
     */
    public function join(&$object)
    {
        $result = true;
        if (method_exists($object, 'getDataForJSON')) {
            $this->chained[] = $object;
        } else {
            throw new \Ease\Exception('$object->getDataForJSON() does not exist');
        }

        return $result;
    }

    /**
     * Prepare record ID to use in URL
     * 
     * @param mixed $id
     * 
     * @return string id ready for use in URL
     */
    public static function urlizeId($id)
    {
        if (is_array($id)) {
            $id = rawurlencode('('.self::flexiUrl($id).')');
        } else if (preg_match('/^ext:/', $id)) {
            $id = self::urlEncode($id);
        } else if (preg_match('/^code:/', $id)) {
            $id = self::code(self::urlEncode(self::uncode($id)));
        }
        return $id;
    }

    /**
     * Test if given record ID exists in FlexiBee.
     *
     * @param mixed $identifer presence state
     *
     * @return boolean
     */
    public function idExists($identifer = null)
    {
        if (is_null($identifer)) {
            $identifer = $this->getMyKey();
        }
        $ignorestate = $this->ignore404();
        $this->ignore404(true);
        $cands       = $this->getFlexiData(null,
            [
                'detail' => 'custom:'.$this->getKeyColumn(),
                $this->getKeyColumn() => $identifer
        ]);
        $this->ignore404($ignorestate);
        return ($this->lastResponseCode == 200) && !empty($cands);
    }

    /**
     * Test if given record exists in FlexiBee.
     *
     * @param array|string|int $data ext:id:23|code:ITEM|['id'=>23]|23
     * 
     * @return boolean Record presence status
     */
    public function recordExists($data = [])
    {

        if (empty($data)) {
            $data = $this->getData();
        }
        $ignorestate = $this->ignore404();
        $this->ignore404(true);
        $keyColumn   = $this->getKeyColumn();
        $res         = $this->getColumnsFromFlexibee([$keyColumn],
            is_array($data) ? $data : [$keyColumn => $data]);

        if (empty($res) || (isset($res['success']) && ($res['success'] == 'false'))
            || ((isset($res) && is_array($res)) && !isset($res[0]) )) {
            $found = false;
        } else {
            $found = true;
        }
        $this->ignore404($ignorestate);
        return $found;
    }

    /**
     * Subitems - ex. items of invoice
     * 
     * @return array of document items or null
     */
    public function getSubItems()
    {
        return array_key_exists('polozkyFaktury', $this->getData()) ? $this->getDataValue('polozkyFaktury')
                : (array_key_exists('polozkyDokladu', $this->getData()) ? $this->getDataValue('polozkyDokladu')
                : null);
    }

    /**
     * Vrací z FlexiBee sloupečky podle podmínek.
     *
     * @param array|int|string $conditions pole podmínek nebo ID záznamu
     * @param string           $indexBy    klice vysledku naplnit hodnotou ze
     *                                     sloupečku
     * @return array
     */
    public function getAllFromFlexibee($conditions = null, $indexBy = null)
    {
        if (is_int($conditions)) {
            $conditions = [$this->getmyKeyColumn() => $conditions];
        }

        $flexiData = $this->getFlexiData('', $conditions);

        if (!is_null($indexBy)) {
            $flexiData = $this->reindexArrayBy($flexiData);
        }

        return $flexiData;
    }

    /**
     * Vrací z FlexiBee sloupečky podle podmínek.
     *
     * @param string|string[] $columnsList seznam položek nebo úrověň detailu: id|summary|full
     * @param array           $conditions  pole podmínek nebo ID záznamu
     * @param string          $indexBy     Sloupeček podle kterého indexovat záznamy
     *
     * @return array
     */
    public function getColumnsFromFlexibee($columnsList, $conditions = [],
                                           $indexBy = null)
    {
        $detail = 'full';
        switch (gettype($columnsList)) {
            case 'integer': //Record ID
                $conditions = [$this->getmyKeyColumn() => $conditions];
            case 'array': //Few Conditions
                if (!is_null($indexBy) && !array_key_exists($indexBy,
                        $columnsList)) {
                    $columnsList[] = $indexBy;
                }
                $columns = implode(',', array_unique($columnsList));
                $detail  = 'custom:'.$columns;
            default:
                switch ($columnsList) {
                    case 'id':
                        $detail = 'id';
                        break;
                    case 'summary':
                        $detail = 'summary';
                        break;
                    default:
                        break;
                }
                break;
        }

        $conditions['detail'] = $detail;

        $flexiData = $this->getFlexiData(null, $conditions);

        if (is_string($indexBy) && is_array($flexiData) && array_key_exists(0,
                $flexiData) && array_key_exists($indexBy, $flexiData[0])) {
            $flexiData = $this->reindexArrayBy($flexiData, $indexBy);
        }

        return $flexiData;
    }

    /**
     * Vrací kód záznamu.
     * Obtain record CODE
     *
     * @param mixed $data
     *
     * @return string
     */
    public function getKod($data = null, $unique = true)
    {
        $kod = null;

        if (is_null($data)) {
            $data = $this->getData();
        }

        if (is_string($data)) {
            $data = [$this->nameColumn => $data];
        }

        if (isset($data['kod'])) {
            $kod = $data['kod'];
        } else {
            if (isset($data[$this->nameColumn])) {
                $kod = preg_replace('/[^a-zA-Z0-9]/', '',
                    \Ease\Sand::rip($data[$this->nameColumn]));
            } else {
                if (isset($data[$this->keyColumn])) {
                    $kod = \Ease\Sand::rip($data[$this->keyColumn]);
                }
            }
            $kod = substr($kod, 0, 20);
        }

        if (!strlen($kod)) {
            $kod = 'NOTSET';
        }

        if (strlen($kod) > 18) {
            $kodfinal = strtoupper(substr($kod, 0, 18));
        } else {
            $kodfinal = strtoupper($kod);
        }

        if ($unique) {
            $counter = 0;
            if (!empty($this->codes) && count($this->codes)) {
                foreach ($this->codes as $codesearch => $keystring) {
                    if (strstr($codesearch, $kodfinal)) {
                        ++$counter;
                    }
                }
            }
            if ($counter) {
                $kodfinal = $kodfinal.$counter;
            }

            $this->codes[$kodfinal] = $kod;
        }

        return self::code($kodfinal);
    }

    /**
     * Write Operation Result.
     *
     * @param array  $resultData
     * @param string $url        URL
     * 
     * @return boolean Log save success
     */
    public function logResult($resultData = null, $url = null)
    {
        $logResult = false;
        if (isset($resultData['success']) && ($resultData['success'] == 'false')) {
            if (isset($resultData['message'])) {
                $this->addStatusMessage($resultData['message'], 'warning');
            }
            $this->addStatusMessage('Error '.$this->lastResponseCode.': '.urldecode($url),
                'warning');
            unset($url);
        }
        if (is_null($resultData)) {
            $resultData = $this->lastResult;
        }
        if (isset($url)) {
            $this->logger->addStatusMessage($this->lastResponseCode.':'.urldecode($url));
        }

        if (isset($resultData['results'])) {
            if ($resultData['success'] == 'false') {
                $status = 'error';
            } else {
                $status = 'success';
            }
            foreach ($resultData['results'] as $result) {
                if (isset($result['request-id'])) {
                    $rid = $result['request-id'];
                } else {
                    $rid = '';
                }
                if (isset($result['errors'])) {
                    foreach ($result['errors'] as $error) {
                        $message = $error['message'];
                        if (isset($error['for'])) {
                            $message .= ' for: '.$error['for'];
                        }
                        if (isset($error['value'])) {
                            $message .= ' value:'.$error['value'];
                        }
                        if (isset($error['code'])) {
                            $message .= ' code:'.$error['code'];
                        }
                        $this->addStatusMessage($rid.': '.$message, $status);
                    }
                }
            }
        }
        return $logResult;
    }

    /**
     * Save RAW Curl Request & Response to files in Temp directory
     */
    public function saveDebugFiles()
    {
        $tmpdir   = sys_get_temp_dir();
        $fname    = $this->evidence.'-'.$this->curlInfo['when'].'.'.$this->format;
        $reqname  = $tmpdir.'/request-'.$fname;
        $respname = $tmpdir.'/response-'.$fname;
        if (file_put_contents($reqname, $this->postFields)) {
            $this->addStatusMessage($reqname, 'debug');
        }
        if (file_put_contents($respname, $this->lastCurlResponse)) {
            $this->addStatusMessage($respname, 'debug');
        }
    }

    /**
     * Připraví data pro odeslání do FlexiBee
     *
     * @param string $data
     */
    public function setPostFields($data)
    {
        $this->postFields = $data;
    }

    /**
     * Get Content ready to be send as POST body
     * @return string
     */
    public function getPostFields()
    {
        return $this->postFields;
    }

    /**
     * Generuje fragment url pro filtrování.
     *
     * @see https://www.flexibee.eu/api/dokumentace/ref/filters
     *
     * @param array  $data   key=>values; value can bee class DatePeriod
     * @param string $joiner default and/or
     * @param string $defop  default operator
     *
     * @return string
     */
    public static function flexiUrl(array $data, $joiner = 'and', $defop = 'eq')
    {
        $parts = [];

        foreach ($data as $column => $value) {
            if (!is_numeric($column)) {
                if (is_integer($data[$column]) || is_float($data[$column])) {
                    $parts[$column] = $column.' eq \''.$data[$column].'\'';
                } elseif (is_bool($data[$column])) {
                    $parts[$column] = $data[$column] ? $column.' eq true' : $column.' eq false';
                } elseif (is_null($data[$column])) {
                    $parts[$column] = $column." is null";
                } elseif (is_object($data[$column])) {
                    switch (get_class($data[$column])) {
                        case 'DatePeriod':
                            $parts[$column] = $column." between '".$data[$column]->getStartDate()->format(self::$DateFormat)."' '".$data[$column]->getEndDate()->format(self::$DateFormat)."'";
                            break;
                        case 'DateTime':
                            $parts[$column] = $column." eq '".$data[$column]->format(self::$DateFormat)."'";
                            break;
                        default:
                            $parts[$column] = $column." $defop '".$data[$column]."'";
                            break;
                    }
                } else {
                    switch ($value) {
                        case '!null':
                            $parts[$column] = $column." is not null";
                            break;
                        case 'is empty':
                        case 'is not empty':
                            $parts[$column] = $column.' '.$value;
                            break;
                        default:
                            switch (explode(' ', trim($value))[0]) {
                                case 'like':
                                case 'begins':
                                case 'ends':
                                    $parts[$column] = $column         .= ' '.$value;
                                    break;
                                default:
                                    if ($column == 'stitky') {
                                        $parts[$column] = $column."='".self::code($data[$column])."'";
                                    } else {
                                        $parts[$column] = $column." $defop '".$data[$column]."'";
                                    }
                                    break;
                            }

                            break;
                    }
                }
            } else {
                $parts[] = $value;
            }
        }
        return implode(' '.$joiner.' ', $parts);
    }

    /**
     * Obtain record/object numeric identificator id:
     * Vrací číselný identifikátor objektu id:
     *
     * @link https://demo.flexibee.eu/devdoc/identifiers Identifikátory záznamů
     *
     * @return null|int indentifikátor záznamu reprezentovaného objektem
     */
    public function getRecordID()
    {
        $id = $this->getDataValue('id');
        return is_null($id) ? null : is_numeric($id) ? intval($id) : $id;
    }

    /**
     * Obtain record/object identificator code:
     * Vrací identifikátor objektu code:
     *
     * @link https://demo.flexibee.eu/devdoc/identifiers Identifikátory záznamů
     *
     * @return string record code identifier
     */
    public function getRecordCode()
    {
        return empty($this->getDataValue('kod')) ? null : self::code($this->getDataValue('kod'));
    }

    /**
     * Obtain record/object identificator extId: code: or id:
     * Vrací identifikátor objektu extId: code: nebo id:
     *
     * @link https://demo.flexibee.eu/devdoc/identifiers Identifikátory záznamů
     *
     * @return string|int|null record code identifier
     */
    public function getRecordIdent()
    {
        $ident = $this->getExternalID();
        if (empty($ident)) {
            $ident = $this->getRecordCode();
        }
        if (empty($ident)) {
            $ident = $this->getRecordID();
        }
        return $ident;
    }

    /**
     * Obtain record/object identificator code: or id:
     * Vrací identifikátor objektu code: nebo id:
     *
     * @link https://demo.flexibee.eu/devdoc/identifiers Identifikátory záznamů
     * 
     * @return string indentifikátor záznamu reprezentovaného objektem
     */
    public function __toString()
    {
        return strval($this->getRecordIdent());
    }

    /**
     * Gives you FlexiPeeHP class name for Given Evidence
     *
     * @param string $evidence
     * 
     * @return string Class name
     */
    public static function evidenceToClassName($evidence)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $evidence)));
    }

    /**
     * Obtain ID of first record in evidence
     *
     * @return string|null id or null if no records
     */
    public function getFirstRecordID()
    {
        $firstID    = null;
        $keyColumn  = $this->getKeyColumn();
        $firstIdRaw = $this->getColumnsFromFlexibee([$keyColumn],
            ['limit' => 1, 'order' => $keyColumn], $keyColumn);
        if (!empty($firstIdRaw) && isset(current($firstIdRaw)[$keyColumn])) {
            $firstID = current($firstIdRaw)[$keyColumn];
        }
        return is_numeric($firstID) ? intval($firstID) : $firstID;
    }

    /**
     * Get previous record ID
     * 
     * @param array $conditions optional
     * 
     * @return int|null
     */
    function getNextRecordID($conditions = [])
    {
        $conditions['order'] = 'id@D';
        $conditions['limit'] = 1;
        $conditions[]        = 'id gt '.$this->getRecordID();
        $next                = $this->getColumnsFromFlexibee(['id'], $conditions);
        return (is_array($next) && array_key_exists(0, $next) && array_key_exists('id',
                $next[0])) ? intval($next[0]['id']) : null;
    }

    /**
     * Get next record ID
     * 
     * @param array $conditions optional
     * 
     * @return int|null
     */
    function getPrevRecordID($conditions = [])
    {
        $conditions['order'] = 'id@A';
        $conditions['limit'] = 1;
        $conditions[]        = 'id lt '.$this->getRecordID();
        $prev                = $this->getColumnsFromFlexibee(['id'], $conditions);
        return (is_array($prev) && array_key_exists(0, $prev) && array_key_exists('id',
                $prev[0])) ? intval($prev[0]['id']) : null;
    }

    /**
     * Vrací hodnotu daného externího ID
     *
     * @param string $want Namespace Selector. If empty,you obtain the first one.
     * 
     * @return string|array one id or array if multiplete
     */
    public function getExternalID($want = null)
    {
        $extid = null;
        $ids   = $this->getExternalIDs();
        if (is_null($want)) {
            if (!empty($ids)) {
                $extid = current($ids);
            }
        } else {
            if (!is_null($ids) && is_array($ids)) {
                foreach ($ids as $id) {
                    if (strstr($id, 'ext:'.$want)) {
                        if (is_null($extid)) {
                            $extid = str_replace('ext:'.$want.':', '', $id);
                        } else {
                            if (is_array($extid)) {
                                $extid[] = str_replace('ext:'.$want.':', '', $id);
                            } else {
                                $extid = [$extid, str_replace('ext:'.$want.':',
                                        '', $id)];
                            }
                        }
                    }
                }
            }
        }
        return $extid;
    }

    /**
     * gives you currently loaded extermal IDs
     * 
     * @return array
     */
    public function getExternalIDs()
    {
        return $this->getDataValue('external-ids');
    }

    /**
     * Obtain actual GlobalVersion
     * Vrací aktuální globální verzi změn
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/changes-api#globalVersion Globální Verze
     * 
     * @return int
     */
    public function getGlobalVersion()
    {
        $this->getFlexiData(null, ['add-global-version' => 'true', 'limit' => 1]);

        return $this->globalVersion;
    }

    /**
     * Gives you current ApiURL with given format suffix
     * 
     * @param string $format json|html|xml|...
     * 
     * @return string API URL for current record or object/evidence
     */
    public function getApiURL($format = null)
    {
        $apiUrl = str_replace(['.'.$this->format, '?limit=0'], '', $this->apiURL);
        return $apiUrl.(empty($format) ? '' : '.'.$format );
    }

    /**
     * Obtain content type of last response
     *
     * @return string
     */
    public function getResponseFormat()
    {
        return $this->responseFormat;
    }

    /**
     * Return the same response format for one and multiplete results
     *
     * @param array $responseBody
     * 
     * @return array
     */
    public function unifyResponseFormat($responseBody)
    {
        if (!is_array($responseBody) || array_key_exists('message',
                $responseBody)) { //Unifi response format
            $response = $responseBody;
        } else {
            $evidence = $this->getResponseEvidence();
            if (array_key_exists($evidence, $responseBody)) {
                $response        = [];
                $evidenceContent = $responseBody[$evidence];
                if (array_key_exists(0, $evidenceContent)) {
                    $response[$evidence] = $evidenceContent; //Multiplete Results
                } else {
                    $response[$evidence][0] = $evidenceContent; //One result
                }
            } else {
                if (isset($responseBody['priloha'])) {
                    $response = $responseBody['priloha'];
                } else {
                    if (array_key_exists('results', $responseBody)) {
                        $response = $responseBody['results'];
                    } else {
                        $response = $responseBody;
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Obtain structure for current (or given) evidence
     *
     * @param string $evidence
     * 
     * @return array Evidence structure
     */
    public function getOfflineColumnsInfo($evidence = null)
    {
        $columnsInfo = null;
        $infoSource  = self::$infoDir.'/Properties.'.(empty($evidence) ? $this->getEvidence()
                : $evidence).'.json';
        if (file_exists($infoSource)) {
            $columnsInfo = json_decode(file_get_contents($infoSource), true);
        }
        return $columnsInfo;
    }

    /**
     * Obtain Current evidence Live structure
     * 
     * @param string $evidence
     * 
     * @return array structure
     */
    public function getOnlineColumnsInfo($evidence = null)
    {
        $properties = [];
        $evidence   = is_null($evidence) ? $this->getEvidence() : $evidence;
        $flexinfo   = $this->performRequest('/c/'.$this->company.'/'.$evidence.'/properties.json');
        if (count($flexinfo) && array_key_exists('properties', $flexinfo)) {
            foreach ($flexinfo['properties']['property'] as $evidenceProperty) {
                $key                      = $evidenceProperty['propertyName'];
                $properties[$key]         = $evidenceProperty;
                $properties[$key]['name'] = $evidenceProperty['name'];
                $properties[$key]['type'] = $evidenceProperty['type'];
                if (array_key_exists('url', $evidenceProperty)) {
                    $properties[$key]['url'] = str_replace('?limit=0', '',
                        $evidenceProperty['url']);
                }
            }
        }
        return $properties;
    }

    /**
     * Update evidence info from array or online from properties.json or offline
     * 
     * @param array  $columnsInfo
     * @param string $evidence
     */
    public function updateColumnsInfo($columnsInfo = null, $evidence = null)
    {
        $evidence = is_null($evidence) ? $this->getEvidence() : $evidence;
        if (is_null($columnsInfo)) {
            $this->columnsInfo[$evidence] = $this->offline ? $this->getOfflineColumnsInfo($evidence)
                    : $this->getOnlineColumnsInfo($evidence);
        } else {
            $this->columnsInfo[$evidence] = $columnsInfo;
        }
    }

    /**
     * Gives you evidence structure. You can obtain current online by pre-calling:
     * $this->updateColumnsInfo($evidence, $this->getOnlineColumnsInfo($evidence));
     * 
     * @param string $evidence
     * 
     * @return array
     */
    public function getColumnsInfo($evidence = null)
    {
        $evidence = is_null($evidence) ? $this->getEvidence() : $evidence;
        if (!array_key_exists($evidence, $this->columnsInfo)) {
            $this->updateColumnsInfo($this->getOfflineColumnsInfo($evidence),
                $evidence);
        }
        return $this->columnsInfo[$evidence];
    }

    /**
     * Gives you properties for (current) evidence column
     *
     * @param string $column    name of column
     * @param string $evidence  evidence name if different
     *
     * @return array column properties or null if column not exits
     */
    public function getColumnInfo($column, $evidence = null)
    {
        $columnsInfo = $this->getColumnsInfo(empty($evidence) ? $this->getEvidence()
                : $evidence);
        return (empty($column) || empty($columnsInfo) || !is_array($columnsInfo))
                ? null : array_key_exists($column, $columnsInfo) ? $columnsInfo[$column]
                : null;
    }

    /**
     * Obtain actions for current (or given) evidence
     *
     * @param string $evidence
     * 
     * @return array Evidence structure
     */
    public function getActionsInfo($evidence = null)
    {
        $actionsInfo = null;
        if (is_null($evidence)) {
            $evidence = $this->getEvidence();
        }
        $propsName = lcfirst(FlexiBeeRO::evidenceToClassName($evidence));
        if (isset(\FlexiPeeHP\Actions::$$propsName)) {
            $actionsInfo = Actions::$$propsName;
        }
        return $actionsInfo;
    }

    /**
     * Obtain relations for current (or given) evidence
     *
     * @param string $evidence
     * 
     * @return array Evidence structure
     */
    public function getRelationsInfo($evidence = null)
    {
        $relationsInfo = null;
        if (is_null($evidence)) {
            $evidence = $this->getEvidence();
        }
        $propsName = lcfirst(FlexiBeeRO::evidenceToClassName($evidence));
        if (isset(\FlexiPeeHP\Relations::$$propsName)) {
            $relationsInfo = Relations::$$propsName;
        }
        return $relationsInfo;
    }

    /**
     * Obtain info for current (or given) evidence
     *
     * @param string $evidence
     * 
     * @return array Evidence info
     */
    public function getEvidenceInfo($evidence = null)
    {
        $evidencesInfo = null;
        if (is_null($evidence)) {
            $evidence = $this->getEvidence();
        }
        if (isset(EvidenceList::$evidences[$evidence])) {
            $evidencesInfo = EvidenceList::$evidences[$evidence];
            $propsName     = lcfirst(FlexiBeeRO::evidenceToClassName($evidence));
            if (isset(Formats::$$propsName)) {
                $evidencesInfo['formats'] = Formats::$$propsName;
            }
        }
        return $evidencesInfo;
    }

    /**
     * Obtain name for current (or given) evidence path
     *
     * @param string $evidence Evidence Path
     * 
     * @return array Evidence info
     */
    public function getEvidenceName($evidence = null)
    {
        $evidenceName = null;
        if (is_null($evidence)) {
            $evidence = $this->getEvidence();
        }
        if (isset(EvidenceList::$name[$evidence])) {
            $evidenceName = EvidenceList::$name[$evidence];
        }
        return $evidenceName;
    }

    /**
     * Save current object to file
     *
     * @param string $destfile path to file
     */
    public function saveResponseToFile($destfile)
    {
        if (strlen($this->lastCurlResponse)) {
            $this->doCurlRequest($this->apiURL, 'GET', $this->format);
        }
        file_put_contents($destfile, $this->lastCurlResponse);
    }

    /**
     * Obtain established relations listing
     *
     * @return array Null or Relations
     */
    public function getVazby($id = null)
    {
        if (is_null($id)) {
            $id = $this->getRecordID();
        }
        if (!empty($id)) {
            $vazbyRaw = $this->getColumnsFromFlexibee(['vazby'],
                ['relations' => 'vazby', 'id' => $id]);
            $vazby    = array_key_exists('vazby', $vazbyRaw[0]) ? $vazbyRaw[0]['vazby']
                    : null;
        } else {
            throw new \Exception(_('ID requied to get record relations '));
        }
        return $vazby;
    }

    /**
     * Gives You URL for Current Record in FlexiBee web interface
     *
     * @return string url
     */
    public function getFlexiBeeURL()
    {
        $parsed_url = parse_url(str_replace('.'.$this->format, '', $this->apiURL));
        $scheme     = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://'
                : '';
        $host       = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port       = isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user       = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass       = isset($parsed_url['pass']) ? ':'.$parsed_url['pass'] : '';
        $pass       = ($user || $pass) ? "$pass@" : '';
        $path       = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        return $scheme.$user.$pass.$host.$port.$path;
    }

    /**
     * Set Record Key
     *
     * @param int|string $myKeyValue
     * 
     * @return boolean
     */
    public function setMyKey($myKeyValue)
    {
        if (substr($myKeyValue, 0, 4) == 'ext:') {
            if ($this->evidenceInfo['extIdSupported'] == 'false') {
                $this->addStatusMessage(sprintf(_('Evidence %s does not support extIDs'),
                        $this->getEvidence()), 'warning');
                $res = false;
            } else {
                $extIds = $this->getDataValue('external-ids');
                if (!empty($extIds) && count($extIds)) {
                    $extIds = array_combine($extIds, $extIds);
                }

                $extIds[$myKeyValue] = $myKeyValue;
                $res                 = $this->setDataValue('external-ids',
                    $extIds);
            }
        } else {
            $res = parent::setMyKey($myKeyValue);
        }
        $this->updateApiURL();
        return $res;
    }

    /**
     * Set or get ignore not found pages flag
     *
     * @param boolean $ignore set flag to
     *
     * @return boolean get flag state
     */
    public function ignore404($ignore = null)
    {
        if (!is_null($ignore)) {
            $this->ignoreNotFound = $ignore;
        }
        return $this->ignoreNotFound;
    }

    /**
     * Send Document by mail
     *
     * @url https://www.flexibee.eu/api/dokumentace/ref/odesilani-mailem/
     *
     * @param string $to         Email ecipient
     * @param string $subject    Email Subject
     * @param string $body       Email Text
     *
     * @return boolean mail sent status
     */
    public function sendByMail($to, $subject, $body, $cc = null)
    {
        $this->setPostFields($body);

        $this->performRequest(rawurlencode($this->getRecordID()).'/odeslani-dokladu?to='.$to.'&subject='.urlencode($subject).'&cc='.$cc
            , 'PUT', 'xml');

        return $this->lastResponseCode == 200;
    }

    /**
     * Send all unsent Documents by eMail
     *
     * @url https://www.flexibee.eu/api/dokumentace/ref/odesilani-mailem/
     * 
     * @return int http response code
     */
    public function sendUnsent()
    {
        return $this->doCurlRequest('automaticky-odeslat-neodeslane', 'PUT',
                'xml');
    }

    /**
     * FlexiBee date to PHP DateTime conversion
     *
     * @param string $flexidate 2017-05-26 or 2017-05-26+02:00
     *
     * @return \DateTime | false
     */
    public static function flexiDateToDateTime($flexidate)
    {
        return \DateTime::createFromFormat(strstr($flexidate, '+') ? self::$DateFormat.'O'
                    : self::$DateFormat, $flexidate)->setTime(0, 0);
    }

    /**
     * FlexiBee dateTime to PHP DateTime conversion
     *
     * @param string $flexidatetime 2017-09-26T10:00:53.755+02:00 or older 2017-05-19T00:00:00+02:00
     *
     * @return \DateTime | false
     */
    public static function flexiDateTimeToDateTime($flexidatetime)
    {
        if (strchr($flexidatetime, '.')) { //NewFormat
            $format = self::$DateTimeFormat;
        } else { // Old format
            $format = 'Y-m-d\TH:i:s+P';
        }
        return \DateTime::createFromFormat($format, $flexidatetime);
    }

    /**
     * Získá dokument v daném formátu
     * Obtain document in given format
     *
     * @link https://www.flexibee.eu/api/dokumentace/ref/pdf/ PDF Exports
     *
     * @param string  $format     pdf/csv/xml/json/ ...
     * @param string  $reportName Template used to generate PDF
     * @param string  $lang       cs|sk|en|de Template language used to generate PDF
     * @param boolean $sign       sign resulting PDF by certificate ?
     *
     * @return string|null filename downloaded or none
     */
    public function getInFormat($format, $reportName = null, $lang = null,
                                $sign = false)
    {
        $response = null;
        if ($this->setFormat($format)) {
            $urlParams = [];
            switch ($format) {
                case 'pdf':
                    switch ($lang) {
                        case 'cs':
                        case 'sk':
                        case 'en':
                        case 'de':
                            $urlParams['report-lang'] = $lang;
                            break;
                        case null:
                        case '':
                            break;
                        default:
                            throw new \Ease\Exception('Unknown language '.$lang.' for PDF export');
                            break;
                    }
                    if (boolval($sign) === true) {
                        $urlParams['report-sign'] = 'true';
                    }
                    break;
                case 'html':
                    $urlParams['inDesktopApp'] = 'true';
                    break;
            }
            if (!empty($reportName)) {
                $urlParams['report-name'] = $reportName;
            }
            if (($this->doCurlRequest(\Ease\Shared::addUrlParams($this->apiURL,
                        $urlParams), 'GET') == 200)) {
                $response = $this->lastCurlResponse;
            }
        }
        return $response;
    }

    /**
     * Uloží dokument v daném formátu do složky v systému souborů
     * Save document in given format to directory in filesystem
     *
     * @param string $format  pdf/csv/xml/json/ ...
     * @param string $destDir where to put file (prefix)
     * @param string $reportName Template used to generate PDF
     *
     * @return string|null filename downloaded or none
     */
    public function downloadInFormat($format, $destDir = './',
                                     $reportName = null)
    {
        $fileOnDisk   = null;
        $formatBackup = $this->format;
        if ($this->setFormat($format)) {
            $downloadTo = $destDir.$this->getEvidence().'_'.$this->getMyKey().'.'.$format;
            if (($this->doCurlRequest(empty($reportName) ? $this->apiURL : \Ease\Shared::addUrlParams($this->apiURL,
                            ['report-name' => $reportName]), 'GET') == 200) && (file_put_contents($downloadTo,
                    $this->lastCurlResponse) !== false)) {
                $fileOnDisk = $downloadTo;
            }
            $this->setFormat($formatBackup);
        }
        return $fileOnDisk;
    }

    /**
     * Take data for object. separate external IDs
     *
     * @param array $data Data to keep
     * 
     * @return int number of records taken
     */
    public function takeData($data)
    {
        $keyColumn = $this->getKeyColumn();
        if (array_key_exists($keyColumn, $data) && is_array($data[$keyColumn])) {
            foreach ($data[$keyColumn] as $recPos => $recordKey) {
                if (substr($recordKey, 0, 4) == 'ext:') {
                    $data['external-ids'][] = $recordKey;
                    unset($data[$keyColumn][$recPos]);
                }
            }
            if (count($data[$keyColumn]) == 1) {
                $data[$keyColumn] = current($data[$keyColumn]);
            }
        }
        $result = parent::takeData($data);

        if (array_key_exists($keyColumn, $data) || array_key_exists('kod', $data)) {
            $this->updateApiURL();
        }

        return $result;
    }

    /**
     * Get Current Evidence reports listing
     * 
     * @link https://www.flexibee.eu/api/dokumentace/casto-kladene-dotazy-pro-api/vyber-reportu-do-pdf/ Výběr reportu do PDF
     * 
     * @return array
     */
    public function getReportsInfo()
    {
        $reports    = [];
        $reportsRaw = $this->getFlexiData($this->getEvidenceURL().'/reports');
        if (!empty($reportsRaw) && array_key_exists('reports', $reportsRaw) && !empty($reportsRaw['reports'])
            && array_key_exists('report', $reportsRaw['reports']) &&
            !empty($reportsRaw['reports']['report'])) {
            if (\Ease\jQuery\Part::isAssoc($reportsRaw['reports']['report'])) {
                $reports = [$reportsRaw['reports']['report']['reportId'] => $reportsRaw['reports']['report']];
            } else {
                $reports = self::reindexArrayBy($reportsRaw['reports']['report'],
                        'reportId');
            }
        }
        return $reports;
    }

    /**
     * Request authSessionId from current server
     * 
     * @link https://www.flexibee.eu/api/dokumentace/ref/login/ description
     * 
     * @param string $username
     * @param string $password
     * @param string $otp       optional onetime password
     * 
     * @return string authUserId or null in case of problems
     */
    public function requestAuthSessionID($username, $password, $otp = null)
    {
        $this->postFields = http_build_query(is_null($otp) ? ['username' => $username,
            'password' => $password] : ['username' => $username, 'password' => $password,
            'otp' => $otp]);
        $response         = $this->performRequest('/login-logout/login', 'POST',
            'json');
        if (array_key_exists('refreshToken', $response)) {
            $this->refreshToken = $response['refreshToken'];
        } else {
            $this->refreshToken = null;
        }
        return array_key_exists('authSessionId', $response) ? $response['authSessionId']
                : null;
    }

    /**
     * Try to Sign in current user to FlexiBee and keep authSessionId
     * 
     * @return boolean sign in success
     */
    public function login()
    {
        $this->authSessionId = $this->requestAuthSessionID($this->user,
            $this->password);
        return $this->lastResponseCode == 200;
    }

    /**
     * End (current's user) session
     * 
     * 
     * @link https://www.flexibee.eu/api/dokumentace/ref/logout Logout Reference
     * 
     * @param string $username force username to sign off
     * 
     * @return array server response
     */
    public function logout($username = null)
    {
        return $this->performRequest('/status/user/'.(is_null($username) ? $this->user
                    : $username).'/logout', 'POST');
    }

    /**
     * Compile and send Report about Error500 to FlexiBee developers
     * If FlexiBee is running on localost try also include java backtrace
     *
     * @param array $errorResponse result of parseError();
     */
    public function error500Reporter($errorResponse)
    {
        $ur = str_replace('/c/'.$this->company, '',
            str_replace($this->url, '', $this->curlInfo['url']));
        if (!array_key_exists($ur, $this->reports)) {
            $tmpdir   = sys_get_temp_dir();
            $myTime   = $this->curlInfo['when'];
            $curlname = $tmpdir.'/curl-'.$this->evidence.'-'.$myTime.'.json';
            file_put_contents($curlname,
                json_encode($this->curlInfo, JSON_PRETTY_PRINT));

            $report = new \Ease\Mailer($this->reportRecipient,
                'Error report 500 - '.$ur);

            $d     = dir($tmpdir);
            while (false !== ($entry = $d->read())) {
                if (strstr($entry, $myTime)) {
                    $ext  = pathinfo($tmpdir.'/'.$entry, PATHINFO_EXTENSION);
                    $mime = Formats::suffixToContentType($ext);
                    $report->addFile($tmpdir.'/'.$entry,
                        empty($mime) ? 'text/plain' : $mime);
                }
            }
            $d->close();

            if ((strstr($this->url, '://localhost') || strstr($this->url,
                    '://127.')) && file_exists('/var/log/flexibee.log')) {

                $fl = fopen('/var/log/'.'flexibee.log', 'r');
                if ($fl) {
                    $tracelog = [];
                    for ($x_pos = 0, $ln = 0, $output = array(); fseek($fl,
                            $x_pos, SEEK_END) !== -1; $x_pos--) {
                        $char = fgetc($fl);
                        if ($char === "\n") {
                            $tracelog[] = $output[$ln];
                            if (strstr($output[$ln], $errorResponse['message'])) {
                                break;
                            }
                            $ln++;
                            continue;
                        }
                        $output[$ln] = $char.((array_key_exists($ln, $output)) ? $output[$ln]
                                : '');
                    }

                    $trace     = implode("\n", array_reverse($tracelog));
                    $tracefile = $tmpdir.'/trace-'.$this->evidence.'-'.$myTime.'.log';
                    file_put_contents($tracefile, $trace);
                    $report->addItem("\n\n".$trace);
                    fclose($fl);
                }
            } else {
                $report->addItem($errorResponse['message']);
            }

            $licenseInfo = $this->performRequest($this->url.'/default-license.json');

            $report->addItem("\n\n".json_encode($licenseInfo['license'],
                    JSON_PRETTY_PRINT));

            if ($report->send()) {
                $this->reports[$ur] = $myTime;
            }
        }
    }

    /**
     * Returns code:CODE
     *
     * @param string $code
     *
     * @return string
     */
    public static function code($code)
    {
        return ((substr($code, 0, 4) == 'ext:') ? $code : 'code:'.strtoupper(self::uncode($code)));
    }

    /**
     * Returns CODE without code: prefix
     *
     * @param string $code
     *
     * @return string
     */
    public static function uncode($code)
    {
        return str_replace(['code:', 'code%3A'], '', $code);
    }

    /**
     * Remove all @ items from array
     *
     * @param array $data original data
     *
     * @return array data without @ columns
     */
    public static function arrayCleanUP($data)
    {
        return array_filter(
            $data,
            function ($key) {
            return !strchr($key, '@');
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Add Info about used user, server and libraries
     *
     * @param string $additions Additional note text
     */
    public function logBanner($additions = null)
    {
        $this->addStatusMessage('FlexiBee '.str_replace('://',
                '://'.$this->user.'@', $this->getApiUrl()).' FlexiPeeHP v'.self::$libVersion.' (FlexiBee '.EvidenceList::$version.') EasePHP Framework v'.\Ease\Atom::$frameworkVersion.' '.$additions,
            'debug');
    }

    /**
     * Reconnect After unserialization
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $this->curlInit();
    }
}
