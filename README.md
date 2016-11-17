# ConsoleProcessManagerBundle
Provides a way to logging all console events

### Configuration

#### Register `ConsoleProcessManagerBundle` in your app.
Add `RedCircle\ConsoleProcessManagerBundle\ConsoleProcessManagerBundle::enable($application);` in your console file in app directory. 

**Example of `AppKernel.php` file:**
```
class AppKernel extends Kernel
{
    public function registerBundles()
    { 
        $bundles = array(
            ...
            new RedCircle\ConsoleProcessManagerBundle\ConsoleProcessManagerBundle(),
        );

    return $bundles;
    }
}