<?php

namespace Application\Provider\Misc;
Interface TypeAuthentificationProvider
{
    const LOCAL = "db";
    const SHIB = "shib";
    const LDAP = "ldap";
    const CAS = "cas";
}