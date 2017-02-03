<?php
namespace CodeItNow\Curl;

/**
 * Make curl request to send or receive data from remote url
 * @author Akhtar Khan <er.akhtarkhan@gmail.com>
 * @package NetworkUtility
 */
class RequestIt {

    protected $request;

    private $options = array(
        'CURLOPT_RETURNTRANSFER'=>true,
        'CURLOPT_USERAGENT'=>'OpenIt'
    );

    private $url = '';

    private $requestType = 'GET';

    private $params = array();

    private $data = '';

    private $headers = array();

    private $response = '';

    public function __construct() {
        
    }
    
    /**
     * Set request url
     * @param string $url
     * @return \CurlRequest
     */
    public function setUrl($url){
        $this->url = $url;
        return $this;
    }
    
    /**
     * Get request url
     * @return string
     */
    public function getUrl(){
        return $this->url;
    }
    
    /**
     * Set request type POST/GET
     * @param string $type
     * @return \CurlRequest
     */
    public function setRequestType($type){
        $this->requestType = strtoupper($type);
        return $this;
    }
    
    /**
     * Get request type POST/GET
     * @return string
     */
    public function getRequestType(){
        return $this->requestType;
    }
    
    /**
     * Set params to send on request url using POST/GET
     * @param array $params
     * @return \CurlRequest
     */
    public function setParams(array $params){
        $this->params = $params;
        return $this;
    }
    
    /**
     * Get params that will use to send on request url using POST/GET
     * @param bool $format
     * @return type
     */
    public function getParams($format=false){
        $response = $this->params;
        if($format===true and $this->requestType == 'GET'){
            if(!empty($this->params)){
                $str = array();
                foreach($this->params as $paramK=>$paramV){
                    $str[] = $paramK.'='.urlencode($paramV);
                }
                $response = implode('&', $str);
            }else{
                $response = '';
            }
        }
        return $response;
    }

    /**
     * Set data to post on request url, like json data
     * @param string $data
     * @return \CurlRequest
     */
    public function setData($data){
        $this->data = $data;
        return $this;
    }
    
    /**
     * Get data which has set to post on request url
     * @return string
     */
    public function getData(){
        return $this->data;
    }

    /**
     * Set header params like content-type
     * @param string $type
     * @param string $value
     * @return \CurlRequest
     */
    public function setHeader($type, $value){
        $this->headers[$type] = $value;
        return $this;
    }
    
    /**
     * Get a header param
     * @param string $type
     * @return string
     */
    public function getHeader($type){
        $this->updateHeaders();
        return $this->headers[$type];
    }

    /**
     * Set header params like content-type
     * @param array $headers
     * @return \CurlRequest
     */
    public function setHeaders(array $headers){
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
    /**
     * Get a header params
     * @param bool $format
     * @return string
     */
    public function getHeaders($format=false){
        $this->updateHeaders();
        $response = $this->headers;
        if($format===true and !empty($this->headers)){
            $_response = array();
            foreach($this->headers as $k=>$v){
                $_response[] = $k.': '.$v;
            }
            $response = $_response;
        }
        return $response;
    }
    
    /**
     * Set option such as CURLOPT_POST
     * @param string $type
     * @param string $value
     * @return \CurlRequest
     */
    public function setOption($type, $value){
        $this->options[$type] = $value;
        return $this;
    }
    
    /**
     * Get option like CURLOPT_POST
     * @param string $type
     * @return string
     */
    public function getOption($type){
        $this->updateOptions();
        return $this->options[$type];
    }
    
    /**
     * Set options such as CURLOPT_POST
     * @param array $options
     * @return \CurlRequest
     */
    public function setOptions(array $options){
        $this->options = array_merge($this->options, $options);
        return $this;
    }
    
    /**
     * Get options like CURLOPT_POST
     * @return array
     */
    public function getOptions(){
        $this->updateOptions();
        return $this->options;
    }

    private function updateOptions(){
        $isPost = 0;
        if($this->requestType!=='GET'){
            $this->options['CURLOPT_POST'] = true;
            if(!empty($this->data)){
                $this->options['CURLOPT_POSTFIELDS'] = $this->data;
            }
            
            if(!empty($this->params)){
                $this->options['CURLOPT_POSTFIELDS'] = $this->params;
            }
        }
        
        if(!empty($this->headers)){
            $this->options['CURLOPT_HTTPHEADER'] = $this->getHeaders(true);
        }
    }
    
    private function updateHeaders(){
        if(!empty($this->data) and $this->requestType!=='GET'){
            $this->headers['Content-Length'] = strlen($this->data);
        }
    }

    /**
     * Make remote request using curl
     * @param string $url
     * @param string $requestType
     * @param array $params
     * @param string $data
     * @param array $headers
     */
    public function send($requestType='GET', $url='', $params=array(), $data='', $headers=array()){
        if(!empty($requestType)){
            $this->setRequestType($requestType);
        }
        if(!empty($url)){
            $this->setUrl($url);
        }
        if(!empty($params)){
            $this->setParams($params);
        }
        if(!empty($data)){
            $this->setData($data);
        }
        if(!empty($headers)){
            $this->setHeaders($headers);
        }
        
        $url = $this->getUrl();
        if($this->requestType=='GET'){
            $queryStr = $this->getParams(true);
            $url .= preg_match('/\?/', $url) ? '&'.$queryStr : '?'.$queryStr ;
        }
        
        $ch = curl_init($url);
        $this->setCurlOptions($ch);
        $this->response = curl_exec($ch);
        if(curl_errno($ch)>0){
            $this->response = 'Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
        }
        curl_close($ch);
        return $this;
    }
    
    /**
     * 
     * @param \CurlObject $ch
     */
    private function setCurlOptions($ch){
        foreach($this->getOptions() as $option=>$value){
            switch ($option){
                case 'CURLOPT_RETURNTRANSFER':
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $value);
                    break;
                case 'CURLOPT_HEADER':
                    curl_setopt($ch, CURLOPT_HEADER, $value);
                    break;
                case 'CURLOPT_FOLLOWLOCATION':
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $value);
                    break;
                case 'CURLOPT_ENCODING':
                    curl_setopt($ch, CURLOPT_ENCODING, $value);
                    break;
                case 'CURLOPT_USERAGENT':
                    curl_setopt($ch, CURLOPT_USERAGENT, $value);
                    break;
                case 'CURLOPT_AUTOREFERER':
                    curl_setopt($ch, CURLOPT_AUTOREFERER, $value);
                    break;
                case 'CURLOPT_CONNECTTIMEOUT':
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $value);
                    break;
                case 'CURLOPT_TIMEOUT':
                    curl_setopt($ch, CURLOPT_TIMEOUT, $value);
                    break;
                case 'CURLOPT_MAXREDIRS':
                    curl_setopt($ch, CURLOPT_MAXREDIRS, $value);
                    break;
                case 'CURLOPT_POST':
                    curl_setopt($ch, CURLOPT_POST, $value);
                    break;
                case 'CURLOPT_POSTFIELDS':
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
                    break;
                case 'CURLOPT_SSL_VERIFYHOST':
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $value);
                    break;
                case 'CURLOPT_VERBOSE':
                    curl_setopt($ch, CURLOPT_VERBOSE, $value);
                    break;
                case 'CURLOPT_HTTPHEADER':
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $value);
                    break;
            }
        }

        if(!in_array($this->requestType, array('GET', 'POST'))){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->requestType);
        }
    }
    
    /**
     * Get response returned by remote url
     * @return string
     */
    public function getResponse(){
        return $this->response;
    }
    
    /**
     * 
     * @param string $filename  Filename with full path
     * @param string $postname  Filename to post with file
     * @param type $fileType    File type
     * @return string
     */
    public function getCurlFile($filename, $postname, $fileType=null)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $fileType, $postname);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename};filename=" . $postname;
        if ($fileType) {
            $value .= ';type=' . $fileType;
        }
        return $value;
    }
    
    /**
     * 
     * @param string $paramName
     * @param string $fileName
     * @param string $newFileName
     * @return \CurlRequest
     */
    public function setFile($paramName, $fileName, $newFileName=''){
        $fileName = realpath($fileName);
        $postname = !empty($newFileName) ? $newFileName : basename($fileName);
        $fileType  = $this->getMimeType($fileName);
        $this->params[$paramName] = $this->getCurlFile($fileName, $postname, $fileType);
        return $this;
    }
    
    /**
     * 
     * @param string $file
     * @return string
     */
    public function getMimeType($file) {
        // MIME types array
        $mimeTypes = array(
            "323"       => "text/h323",
            "acx"       => "application/internet-property-stream",
            "ai"        => "application/postscript",
            "aif"       => "audio/x-aiff",
            "aifc"      => "audio/x-aiff",
            "aiff"      => "audio/x-aiff",
            "asf"       => "video/x-ms-asf",
            "asr"       => "video/x-ms-asf",
            "asx"       => "video/x-ms-asf",
            "au"        => "audio/basic",
            "avi"       => "video/x-msvideo",
            "axs"       => "application/olescript",
            "bas"       => "text/plain",
            "bcpio"     => "application/x-bcpio",
            "bin"       => "application/octet-stream",
            "bmp"       => "image/bmp",
            "c"         => "text/plain",
            "cat"       => "application/vnd.ms-pkiseccat",
            "cdf"       => "application/x-cdf",
            "cer"       => "application/x-x509-ca-cert",
            "class"     => "application/octet-stream",
            "clp"       => "application/x-msclip",
            "cmx"       => "image/x-cmx",
            "cod"       => "image/cis-cod",
            "cpio"      => "application/x-cpio",
            "crd"       => "application/x-mscardfile",
            "crl"       => "application/pkix-crl",
            "crt"       => "application/x-x509-ca-cert",
            "csh"       => "application/x-csh",
            "css"       => "text/css",
            "dcr"       => "application/x-director",
            "der"       => "application/x-x509-ca-cert",
            "dir"       => "application/x-director",
            "dll"       => "application/x-msdownload",
            "dms"       => "application/octet-stream",
            "doc"       => "application/msword",
            "dot"       => "application/msword",
            "dvi"       => "application/x-dvi",
            "dxr"       => "application/x-director",
            "eps"       => "application/postscript",
            "etx"       => "text/x-setext",
            "evy"       => "application/envoy",
            "exe"       => "application/octet-stream",
            "fif"       => "application/fractals",
            "flr"       => "x-world/x-vrml",
            "gif"       => "image/gif",
            "gtar"      => "application/x-gtar",
            "gz"        => "application/x-gzip",
            "h"         => "text/plain",
            "hdf"       => "application/x-hdf",
            "hlp"       => "application/winhlp",
            "hqx"       => "application/mac-binhex40",
            "hta"       => "application/hta",
            "htc"       => "text/x-component",
            "htm"       => "text/html",
            "html"      => "text/html",
            "htt"       => "text/webviewhtml",
            "ico"       => "image/x-icon",
            "ief"       => "image/ief",
            "iii"       => "application/x-iphone",
            "ins"       => "application/x-internet-signup",
            "isp"       => "application/x-internet-signup",
            "jfif"      => "image/pipeg",
            "jpe"       => "image/jpeg",
            "jpeg"      => "image/jpeg",
            "jpg"       => "image/jpeg",
            "js"        => "application/x-javascript",
            "latex"     => "application/x-latex",
            "lha"       => "application/octet-stream",
            "lsf"       => "video/x-la-asf",
            "lsx"       => "video/x-la-asf",
            "lzh"       => "application/octet-stream",
            "m13"       => "application/x-msmediaview",
            "m14"       => "application/x-msmediaview",
            "m3u"       => "audio/x-mpegurl",
            "man"       => "application/x-troff-man",
            "mdb"       => "application/x-msaccess",
            "me"        => "application/x-troff-me",
            "mht"       => "message/rfc822",
            "mhtml"     => "message/rfc822",
            "mid"       => "audio/mid",
            "mny"       => "application/x-msmoney",
            "mov"       => "video/quicktime",
            "movie"     => "video/x-sgi-movie",
            "mp2"       => "video/mpeg",
            "mp3"       => "audio/mpeg",
            "mpa"       => "video/mpeg",
            "mpe"       => "video/mpeg",
            "mpeg"      => "video/mpeg",
            "mpg"       => "video/mpeg",
            "mpp"       => "application/vnd.ms-project",
            "mpv2"      => "video/mpeg",
            "ms"        => "application/x-troff-ms",
            "mvb"       => "application/x-msmediaview",
            "nws"       => "message/rfc822",
            "oda"       => "application/oda",
            "p10"       => "application/pkcs10",
            "p12"       => "application/x-pkcs12",
            "p7b"       => "application/x-pkcs7-certificates",
            "p7c"       => "application/x-pkcs7-mime",
            "p7m"       => "application/x-pkcs7-mime",
            "p7r"       => "application/x-pkcs7-certreqresp",
            "p7s"       => "application/x-pkcs7-signature",
            "pbm"       => "image/x-portable-bitmap",
            "pdf"       => "application/pdf",
            "pfx"       => "application/x-pkcs12",
            "pgm"       => "image/x-portable-graymap",
            "pko"       => "application/ynd.ms-pkipko",
            "pma"       => "application/x-perfmon",
            "pmc"       => "application/x-perfmon",
            "pml"       => "application/x-perfmon",
            "pmr"       => "application/x-perfmon",
            "pmw"       => "application/x-perfmon",
            "pnm"       => "image/x-portable-anymap",
            "pot"       => "application/vnd.ms-powerpoint",
            "ppm"       => "image/x-portable-pixmap",
            "pps"       => "application/vnd.ms-powerpoint",
            "ppt"       => "application/vnd.ms-powerpoint",
            "prf"       => "application/pics-rules",
            "ps"        => "application/postscript",
            "pub"       => "application/x-mspublisher",
            "qt"        => "video/quicktime",
            "ra"        => "audio/x-pn-realaudio",
            "ram"       => "audio/x-pn-realaudio",
            "ras"       => "image/x-cmu-raster",
            "rgb"       => "image/x-rgb",
            "rmi"       => "audio/mid",
            "roff"      => "application/x-troff",
            "rtf"       => "application/rtf",
            "rtx"       => "text/richtext",
            "scd"       => "application/x-msschedule",
            "sct"       => "text/scriptlet",
            "setpay"    => "application/set-payment-initiation",
            "setreg"    => "application/set-registration-initiation",
            "sh"        => "application/x-sh",
            "shar"      => "application/x-shar",
            "sit"       => "application/x-stuffit",
            "snd"       => "audio/basic",
            "spc"       => "application/x-pkcs7-certificates",
            "spl"       => "application/futuresplash",
            "src"       => "application/x-wais-source",
            "sst"       => "application/vnd.ms-pkicertstore",
            "stl"       => "application/vnd.ms-pkistl",
            "stm"       => "text/html",
            "svg"       => "image/svg+xml",
            "sv4cpio"   => "application/x-sv4cpio",
            "sv4crc"    => "application/x-sv4crc",
            "t"         => "application/x-troff",
            "tar"       => "application/x-tar",
            "tcl"       => "application/x-tcl",
            "tex"       => "application/x-tex",
            "texi"      => "application/x-texinfo",
            "texinfo"   => "application/x-texinfo",
            "tgz"       => "application/x-compressed",
            "tif"       => "image/tiff",
            "tiff"      => "image/tiff",
            "tr"        => "application/x-troff",
            "trm"       => "application/x-msterminal",
            "tsv"       => "text/tab-separated-values",
            "txt"       => "text/plain",
            "uls"       => "text/iuls",
            "ustar"     => "application/x-ustar",
            "vcf"       => "text/x-vcard",
            "vrml"      => "x-world/x-vrml",
            "wav"       => "audio/x-wav",
            "wcm"       => "application/vnd.ms-works",
            "wdb"       => "application/vnd.ms-works",
            "wks"       => "application/vnd.ms-works",
            "wmf"       => "application/x-msmetafile",
            "wps"       => "application/vnd.ms-works",
            "wri"       => "application/x-mswrite",
            "wrl"       => "x-world/x-vrml",
            "wrz"       => "x-world/x-vrml",
            "xaf"       => "x-world/x-vrml",
            "xbm"       => "image/x-xbitmap",
            "xla"       => "application/vnd.ms-excel",
            "xlc"       => "application/vnd.ms-excel",
            "xlm"       => "application/vnd.ms-excel",
            "xls"       => "application/vnd.ms-excel",
            "xlsx"      => "vnd.ms-excel",
            "xlt"       => "application/vnd.ms-excel",
            "xlw"       => "application/vnd.ms-excel",
            "xof"       => "x-world/x-vrml",
            "xpm"       => "image/x-xpixmap",
            "xwd"       => "image/x-xwindowdump",
            "z"         => "application/x-compress",
            "zip"       => "application/zip"
        );

        $extension = end(explode('.', $file));
        return $mimeTypes[$extension]; // return the array value
    }
    
}
