<?php

namespace lib\http;

enum RouteMethod
{
    case GET;
    case POST;
    case PUT;
    case DELETE;
    case OPTIONS;
}