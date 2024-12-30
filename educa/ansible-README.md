# educa constructable test suite
Ansible playbooks to set up or update a stupla web development environment.
Working in debian based linux distros (e.g. Ubuntu) or in Windows using said distro via WSL1 (WSL2 is also supported, but marginally decreases performance during migration; uncomment to use WSL2).

## Files
- **ansible.env** - Sample environment configuration using the variables already specified in ansible-update.yaml 
- **ansible-install.yaml** - Initial installation of all tools and prerequisites
- **ansible-update.yaml** - (Re-)installing and migrating the development environment (Run after new migration / after dependency change / for cleanup)

## Usage
- Install ansible using ```sudo apt update && sudo apt install ansible -y```
- Clone this repository including all submodules and rename the ```ansible.env``` to ```.env```
- Run any playbook in this repository (with default permissions), e.g. ```ansible-playbook ansible-install.yaml --ask-become-pass -v``` (```BECOME password``` = sudo password for authorization)
- *[Optional]* Add configurations to run ```npm watch``` and ```php -S [...] -t [...]``` to your IDE <br/>
  (If you're using PHP Storm with a Windows Host, you can add a npm script with WSL interpreter and run the PHP server via a shell script configuration using ```wsl -d [...] -e php -S [...] -t [...]```)
