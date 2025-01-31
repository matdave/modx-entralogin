#!/usr/bin/env sh

kiota generate -l PHP -d entra-modx.yml -c GraphApiClient -n MODX\\EntraLogin\\Client -o ./src/Client