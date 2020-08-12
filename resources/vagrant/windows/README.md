# Run tests on Windows

``` bash
cd /path/to/conventional-commits/resources/vagrant/windows
vagrant up
vagrant ssh
```

Once inside the VM:

``` bash
refreshenv
cd conventional-commits
composer install
composer test
```
