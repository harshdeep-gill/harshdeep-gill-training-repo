# Media Migration command

Migration script for media from drupal to WordPress.


## Usage

1. [Media migration](#media-commands)
2. [Media metadata migration](#media-metadata-migration)

---

### Media Commands
To migrate all media files from Drupal to WordPress, run the following command:

```bash
wp quark-migrate media all
```

#### Arguments

**--ids=<1,2>**
- If you want to migrate only specific media files, you can pass a list of media IDs separated by commas.

```
wp quark-migrate media all --ids=1,2,3,4
```

**--from-id=\<number>**
- Media ID from where to start migration.

```
wp quark migrate media all --from-id=1
```

**--chunk=\<number>**
- Please provide the number of the chunk that needs to be migrated.

```
wp quark migrate media all --chunk=1 --total-chunks=50
```

**--total-chunks=\<number>**
- Total number of items per chunk.

```
wp quark migrate media all --chunk=1 --total-chunks=50
```
---

### Media Metadata migration

Once media migration completed successfully, you can run the following command to migrate media metadata:

```bash
wp quark-migrate media metadata
```
