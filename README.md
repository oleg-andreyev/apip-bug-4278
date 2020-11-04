Ecentria API Skeleton
=====================

This is the skeleton project for an API.
It uses:
- API Platform
- Doctrine
- Ecentria Logging
- Php Unit
- Deployer

It has pre-configured:
- Kubernetes testing environment
- Deployer phar configurations

How to start?
---

**Clone & Configure**

```
git clone ssh://git@bitbucket.ecentria.tools/svc/ecentria-api-skeleton.git ./<name>
rm -rf ./.git
```

1. **./composer.json** <br>
Change appropriate fields according to your project.
Name, description, homepage, author and etc.
2. **./config/packages/ecentria.yaml** <br>
Set _application\_name_, it will define an index name for your logs in Kibana.
3. **./ci/deployer/Inventory/hosts.yml** <br>
When production server is ready, this file should be configured
accordingly. <hostname>, <user>, <repository> should be replaced with
a real values.
4. **./ci/project.sh** <br>
Configure variables.
_PROJECT\_NAMESPACE_ is used to differ one project from another in Kubernetes
where your tests will be run in. In order to create your kubernetes namespace
visit https://rancher.ecentria.tools/. Contact **Chuck Norris** team for any questions
related to Kubernetes.

Environment variables
---

Production: <br /><br />
Environment variables files are usually managed by puppet. <br />
Deployer.phar plays a crucial role in storing and using .env.local file on production server. <br />
In order to have environment files on production puppet should put .env.local file into:
```bash
/home/<application>/shared/.env.local
```
Deployer will guarantee that this file will be shared across all releases.

<br />
As of Symfony 5.1:
- use `.env.local` for generic overrides on machine (local, remote)
- use `.env.<environment>` (commit to repo) for specific environment vars 
- use `.env.<environment>.local` for specific environment overrides

For more details see: 
- https://symfony.com/doc/current/configuration.html#configuring-environment-variables-in-env-files
- https://symfony.com/doc/current/configuration/dot-env-changes.html