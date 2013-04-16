## ![Ensemble Video logo](ext_chooser/css/images/logo.png) Ensemble Video Moodle Repository Plugin

### Overview

Along with the [Ensemble Video Moodle Filter Plugin](https://github.com/jmpease/moodle-filter_ensemble), this plugin
makes it easier for Moodle users to add videos and playlists to content without
having to navigate to Ensemble Video and copy/paste complicated embed codes.  This
plugin provides additional video and playlist repositories in the content editor that enable
Moodle users to search and choose Ensemble Video media to be added.

**Note:** This plugin requires an Ensemble Video version of 3.4 or higher.

### Installing from Git

These installation instructions are based off the strategy endorsed by Moodle
for [installing contributed extensions via Git](http://docs.moodle.org/24/en/Git_for_Administrators#Installing_a_contributed_extension_from_its_Git_repository).

    $ cd /path/to/your/moodle
    $ cd repository
    $ git clone https://github.com/jmpease/moodle-repository_ensemble.git ensemble
    $ cd ensemble
    $ git checkout -b MOODLE_24_STABLE origin/MOODLE_24_STABLE


### Installing from ZIP

    $ wget https://github.com/jmpease/moodle-repository_ensemble/archive/MOODLE_24_STABLE.zip
    $ unzip MOODLE_24_STABLE.zip
    $ mv moodle-repository_ensemble-MOODLE_24_STABLE /path/to/your/moodle/repository/ensemble


**Note:** Regardless of the installation method above, you also need to install the required [filter extension](https://github.com/jmpease/moodle-filter_ensemble).


### Plugin Setup

As a Moodle administrator, navigate to Settings -> Site Administration -> Notifications
and click 'Upgrade Moodle database now' to install the plugin.

Next navigate to Settings -> Site Administration -> Plugins -> Repositories -> Manage repositories
and set the Ensemble Video repository to 'Enabled and visible'.

#### Configuration Settings

##### Ensemble URL
Required setting.  Must point to the application root of your Ensemble Video
installation.  If, for example, the url for your Ensemble install is
'https://cloud.ensemblevideo.com/app/library.aspx', you would use
'https://cloud.ensemblevideo.com'.  In the case of a url like
'https://server.myschool.edu/ensemble/app/library.aspx' you would use
'https://server.myschool.edu/ensemble'.

##### Service Account Username (optional)

Optional.  If left empty, users of the repository will be prompted to
authenticate with their Ensemble Video credentials. Otherwise, this can be set
to a "service account" (an Ensemble Video account with a "System Administrator"
role) that has access to all content for your Moodle user population within
Ensemble Video.  The plugin will use this account to query the Ensemble Video
API, but will filter results by the username of the currently logged in Moodle
user.  With this approach users won't have to authenticate to Ensemble Video,
but it does imply that Moodle and Ensemble Video usernames match.

##### Service Account Password (optional)

Optional.  Used along with the Service Account Username as the credentials used
to query the Ensemble Video API.  See above.

##### Ensemble Account Domain (optional)

Optional.  Used to specify an Ensemble Video authentication domain to be used
when filtering results by Moodle username.  This is only used when the Service
Account Username is set and is appended to the currently authenticated Moodle
username.


Once setup, you should see two additional repositories under 'Insert Moodle media' in the
content editor, one for choosing videos and another for choosing playlists from the
configured Ensemble Video installation.
