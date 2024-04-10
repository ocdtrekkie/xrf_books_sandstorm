@0xd88cd6b1beff24a7;

using Spk = import "/sandstorm/package.capnp";
# This imports:
#   $SANDSTORM_HOME/latest/usr/include/sandstorm/package.capnp
# Check out that file to see the full, documented package definition format.

const pkgdef :Spk.PackageDefinition = (
  # The package definition. Note that the spk tool looks specifically for the
  # "pkgdef" constant.

  id = "rp7kx466n6n6tt68gddt76er56c5a0w2waejj56deh1tw9h9h7x0",
  # Your app ID is actually its public key. The private key was placed in
  # your keyring. All updates must be signed with the same key.

  manifest = (
    # This manifest is included in your app package to tell Sandstorm
    # about your app.

    appTitle = (defaultText = "XRF Books"),

    appVersion = 0,  # Increment this for every release.

    appMarketingVersion = (defaultText = "0.0.1"),
    # Human-readable representation of appVersion. Should match the way you
    # identify versions of your app in documentation and marketing.

    actions = [
      # Define your "new document" handlers here.
      ( nounPhrase = (defaultText = "library"),
        command = .myCommand
        # The command to run when starting for the first time. (".myCommand"
        # is just a constant defined at the bottom of the file.)
      )
    ],

    continueCommand = .myCommand,
    # This is the command called to start your app back up after it has been
    # shut down for inactivity. Here we're using the same command as for
    # starting a new instance, but you could use different commands for each
    # case.

    metadata = (
      icons = (
        appGrid = (svg = embed "emyller-books.svg"),
        grain = (svg = embed "emyller-books.svg"),
        market = (svg = embed "emyller-books.svg"),
        marketBig = (svg = embed "emyller-books.svg"),
      ),

      website = "https://github.com/ocdtrekkie/xrf_books_sandstorm",

      codeUrl = "https://github.com/ocdtrekkie/xrf_books_sandstorm",

      license = (openSource = mit),

      categories = [media],

      author = (
        # Fields relating to the author of this app.

        contactEmail = "inbox@jacobweisz.com",

        #pgpSignature = embed "path/to/pgp-signature",
        # PGP signature attesting responsibility for the app ID. This is a binary-format detached
        # signature of the following ASCII message (not including the quotes, no newlines, and
        # replacing <app-id> with the standard base-32 text format of the app's ID):
        #
        # "I am the author of the Sandstorm.io app with the following ID: <app-id>"
        #
        # You can create a signature file using `gpg` like so:
        #
        #     echo -n "I am the author of the Sandstorm.io app with the following ID: <app-id>" | gpg --sign > pgp-signature
        #
        # Further details including how to set up GPG and how to use keybase.io can be found
        # at https://docs.sandstorm.io/en/latest/developing/publishing-apps/#verify-your-identity
      ),

      #pgpKeyring = embed "path/to/pgp-keyring",
      # A keyring in GPG keyring format containing all public keys needed to verify PGP signatures in
      # this manifest (as of this writing, there is only one: `author.pgpSignature`).
      #
      # To generate a keyring containing just your public key, do:
      #
      #     gpg --export <key-id> > keyring
      #
      # Where `<key-id>` is a PGP key ID or email address associated with the key.

      description = (defaultText = embed "../README.md"),

      shortDescription = (defaultText = "Library catalog"),

      screenshots = [
        # Screenshots to use for marketing purposes.  Examples below.
        # Sizes are given in device-independent pixels, so if you took these
        # screenshots on a Retina-style high DPI screen, divide each dimension by two.

        #(width = 746, height = 795, jpeg = embed "path/to/screenshot-1.jpeg"),
        #(width = 640, height = 480, png = embed "path/to/screenshot-2.png"),
      ],
      changeLog = (defaultText = embed "../CHANGELOG.md"),
    ),
  ),

  sourceMap = (
    # Here we defined where to look for files to copy into your package. The
    # `spk dev` command actually figures out what files your app needs
    # automatically by running it on a FUSE filesystem. So, the mappings
    # here are only to tell it where to find files that the app wants.
    searchPath = [
      ( sourcePath = "." ),  # Search this directory first.
      ( sourcePath = "/",    # Then search the system root directory.
        hidePaths = [ "home", "proc", "sys",
                      "etc/passwd", "etc/hosts", "etc/host.conf",
                      "etc/nsswitch.conf", "etc/resolv.conf" ]
        # You probably don't want the app pulling files from these places,
        # so we hide them. Note that /dev, /var, and /tmp are implicitly
        # hidden because Sandstorm itself provides them.
      )
    ]
  ),

  fileList = "sandstorm-files.list",
  # `spk dev` will write a list of all the files your app uses to this file.
  # You should review it later, before shipping your app.

  alwaysInclude = [],
  # Fill this list with more names of files or directories that should be
  # included in your package, even if not listed in sandstorm-files.list.
  # Use this to force-include stuff that you know you need but which may
  # not have been detected as a dependency during `spk dev`. If you list
  # a directory here, its entire contents will be included recursively.

  bridgeConfig = (
    # Used for integrating permissions and roles into the Sandstorm shell
    # and for sandstorm-http-bridge to pass to your app.
    # Uncomment this block and adjust the permissions and roles to make
    # sense for your app.
    # For more information, see high-level documentation at
    # https://docs.sandstorm.io/en/latest/developing/auth/
    # and advanced details in the "BridgeConfig" section of
    # https://github.com/sandstorm-io/sandstorm/blob/master/src/sandstorm/package.capnp
    viewInfo = (
      # For details on the viewInfo field, consult "ViewInfo" in
      # https://github.com/sandstorm-io/sandstorm/blob/master/src/sandstorm/grain.capnp
  
      permissions = [
      # Permissions which a user may or may not possess.  A user's current
      # permissions are passed to the app as a comma-separated list of `name`
      # fields in the X-Sandstorm-Permissions header with each request.
      #
      # IMPORTANT: only ever append to this list!  Reordering or removing fields
      # will change behavior and permissions for existing grains!  To deprecate a
      # permission, or for more information, see "PermissionDef" in
      # https://github.com/sandstorm-io/sandstorm/blob/master/src/sandstorm/grain.capnp
        (
          name = "admin",
          # Name of the permission, used as an identifier for the permission in cases where string
          # names are preferred.  Used in sandstorm-http-bridge's X-Sandstorm-Permissions HTTP header.
  
          title = (defaultText = "admin"),
          # Display name of the permission, e.g. to display in a checklist of permissions
          # that may be assigned when sharing.
  
          description = (defaultText = "ability to administrate the system"),
          # Prose describing what this role means, suitable for a tool tip or similar help text.
        ),
        (
          name = "operate",
          title = (defaultText = "operate"),
          description = (defaultText = "ability to handle circulation"),
        ),
		(
          name = "restrictedarea",
          title = (defaultText = "restrictedarea"),
          description = (defaultText = "restricted material access"),
        ),
        (
          name = "digitalbrowse",
          title = (defaultText = "digitalbrowse"),
          description = (defaultText = "digital library access"),
        ),
		(
          name = "physicalbrowse",
          title = (defaultText = "physicalbrowse"),
          description = (defaultText = "physical library access"),
        ),
      ],
      roles = [
        # Roles are logical collections of permissions.  For instance, your app may have
        # a "viewer" role and an "editor" role
        (
          title = (defaultText = "administrator"),
          # Name of the role.  Shown in the Sandstorm UI to indicate which users have which roles.
  
          permissions  = [true,true,true,true,true],
          # An array indicating which permissions this role carries.
          # It should be the same length as the permissions array in
          # viewInfo, and the order of the lists must match.
  
          verbPhrase = (defaultText = "can do anything"),
          # Brief explanatory text to show in the sharing UI indicating
          # what a user assigned this role will be able to do with the grain.
  
          description = (defaultText = "administrators can edit and configure all settings."),
          # Prose describing what this role means, suitable for a tool tip or similar help text.
        ),
		(
          title = (defaultText = "librarian"),
          permissions  = [false,true,true,true,true],
          verbPhrase = (defaultText = "can handle circulation"),
          description = (defaultText = "moderators can perform limited administrative functions."),
        ),
		(
          title = (defaultText = "universal patron"),
          permissions  = [false,false,false,true,true],
          verbPhrase = (defaultText = "can browse all materials"),
          description = (defaultText = "universal patrons can browse all materials."),
        ),
		(
          title = (defaultText = "digital patron"),
          permissions  = [false,false,false,true,false],
          verbPhrase = (defaultText = "can browse digital materials"),
          description = (defaultText = "digital patrons can browse digital materials."),
        ),
        (
          title = (defaultText = "physical patron"),
          permissions  = [false,false,false,false,true],
          verbPhrase = (defaultText = "can browse physical materials"),
          description = (defaultText = "physical patrons can browse physical materials."),
        ),
      ],
    ),
    #apiPath = "/api",
    # Apps can export an API to the world.  The API is to be used primarily by Javascript
    # code and native apps, so it can't serve out regular HTML to browsers.  If a request
    # comes in to your app's API, sandstorm-http-bridge will prefix the request's path with
    # this string, if specified.
  ),
);

const myCommand :Spk.Manifest.Command = (
  # Here we define the command used to start up your server.
  argv = ["/sandstorm-http-bridge", "8000", "--", "/bin/bash", "/opt/app/.sandstorm/launcher.sh"],
  environ = [
    # Note that this defines the *entire* environment seen by your app.
    (key = "PATH", value = "/usr/local/bin:/usr/bin:/bin"),
    (key = "SANDSTORM", value = "1"),
    # Export SANDSTORM=1 into the environment, so that apps running within Sandstorm
    # can detect if $SANDSTORM="1" at runtime, switching UI and/or backend to use
    # the app's Sandstorm-specific integration code.
  ]
);
