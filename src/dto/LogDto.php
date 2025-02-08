<?php

namespace aigletter\logging\dto;

class LogDto
{
    /**
     * @var string
     */
    public string $id;

    /**
     * %a - remoteIp - Remote IP-address
     * @var string
     */
    public $remoteAddr;

    /**
     * %u - user - Remote user (from auth; may be bogus if return status (%s) is 401)
     * @var string
     */
    public $remoteUser;

    /**
     * %t - time - Time the request was received (standard english format)
     * @var
     */
    public $timeLocal;

    /**
     * %r - request - First line of request
     * @var
     */
    public $request;

    /**
     * %>s - status - status
     * @var
     */
    public $status;

    /**
     * %O - sentBytes - Bytes sent, including headers, cannot be zero. You need to enable mod_logio to use this.
     * @var
     */
    public $bodyBytesSent;

    /**
     * %{Foobar}i - HeaderReferer - The contents of Foobar: header line(s) in the request sent to the server.
     * Changes made by other modules (e.g. mod_headers) affect this.
     * If you're interested in what the request header was prior to when most modules would have modified it,
     * use mod_setenvif to copy the header into an internal environment variable and log that value
     * with the %{VARNAME}e described above.
     * @var
     */
    public $httpReferer;

    /**
     * %{Foobar}i - HeaderUserAgent - The contents of Foobar: header line(s) in the request sent to the server.
     * Changes made by other modules (e.g. mod_headers) affect this.
     * If you're interested in what the request header was prior to when most modules would have modified it,
     * use mod_setenvif to copy the header into an internal environment variable and log that value
     * with the %{VARNAME}e described above.
     * @var
     */
    public $httpUserAgent;

    /**
     * Origin line
     * @var string
     */
    public $origin;



    /*%%% FOR for FUTURE %%%*/

    /**
     * The percent sign
     * @var string
     */
    //public $percent;

    /**
      * %A - Local IP-address
      * @var
      */
    //public $localIp;

    /**
      * %b - Size of response in bytes, excluding HTTP headers. In CLF format, i.e. a '-' rather than a 0 when no bytes are sent.
      * @var
      */
    //public $responseBytes;

    /**
      * %D - The time taken to serve the request, in microseconds.
      * @var
      */
    //public $timeServeRequest;

    /**
      * %h - Remote host
      * @var
      */
    //public $host;

    /**
      * %I - Bytes received, including request and headers, cannot be zero. You need to enable mod_logio to use this.
      * @var
      */
    //public $receivedBytes;

    /**
     * %l - Remote logname (from identd, if supplied). This will return a dash unless mod_ident is present and
     * IdentityCheck is set On.
     * @var
     */
    //public $logname;

    /**
      * %m - The request method
      * @var
      */
    //public $requestMethod;

    /**
      * %p - The canonical port of the server serving the request
      * @var
      */
    //public $port;

    /**
      * %S - This is nginx specific: https://nginx.org/en/docs/http/ngx_http_core_module.html#var_scheme
      * @var
      */
    //public $scheme;

    /**
      * %T - The time taken to serve the request, in seconds. This option is not consistent, Apache won't inform the milisecond part.
      * @var
      */
    //public $requestTime;

    /**
      * %U - The URL path requested, not including any query string.
      * @var
      */
    //public $URL;

    /**
      * %v - The canonical ServerName of the server serving the request.
      * @var
      */
    //public $serverName;

    /**
      * %V - The server name according to the UseCanonicalName setting.
      * @var
      */
    //public $canonicalServerName;

    /**
     * %{Foobar}i - The contents of Foobar: header line(s) in the request sent to the server. Changes made by other
     * modules (e.g. mod_headers) affect this. If you're interested in what the request header was prior to when most
     * modules would have modified it, use mod_setenvif to copy the header into an internal environment variable and
     * log that value with the %{VARNAME}e described above.
     * @var
     */
    //public $_Header;

    /**
     * %{format}p - The canonical port of the server serving the request or the server's actual port or the client's
     * actual port. Valid formats are canonical, local, or remote.
     */
    //public $_Port;
}