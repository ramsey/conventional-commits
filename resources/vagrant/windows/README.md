# Run tests on Windows

``` bash
cd /path/to/project/resources/vagrant/windows
vagrant up
vagrant ssh
```

Once inside the VM:

``` bash
refreshenv
cd project
composer install
composer run-script --timeout=0 test
```
