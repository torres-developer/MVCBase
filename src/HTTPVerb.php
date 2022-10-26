<?php

namespace TorresDeveloper\MVC;

enum HTTPVerb: string {
    case OPTIONS = "OPTIONS";
    case GET = "GET";
    case HEAD = "HEAD";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
    case TRACE = "TRACE";
    case CONNECT = "CONNECT";
}

