<?php
// vim: noet sw=4 sts=4 ts=4

/**
 * Targeting PHP 5.2
 */

/**
 * Return false for uninteresting entries (. and ..)
 *
 * @param string $filename The filename to consider
 * @return bool Boolean true if not an uninteresting filename
 */
function isInteresting($filename) {
	return $filename !== '.' && $filename !== '..';
}

/**
 * Check whether a string should be displayed
 *
 * @param string $filename The filename to consider
 * @return bool Boolean true if displayable
 */
function isDisplayable($filename) {
	// A list of exclusions: these are regex patterns
	$exclusions = array(
		'/^\./', // All dotfiles
		'/^index\.php$/',
	);

	// Loop through exclusion patterns
	foreach ($exclusions as $pattern) {
		if (preg_match($pattern, $filename)) {
			// Exclusion pattern matched filename; reject this filename
			return false;
		}
	}

	// Filename didn't match any exclusion pattern
	return true;
}

/**
 * Get stats about a file in a given directory
 *
 * @param string $filename The file within the directory
 * @param string $directoryPath The path of the file's containing directory
 * @return array Structured data about the file
 */
function fileData($filename, $directoryPath) {
	$fullPath = $directoryPath . DIRECTORY_SEPARATOR . $filename;
	$type = filetype($fullPath);
	$data = array(
		'filename' => $filename,
		'directoryPath' => $directoryPath,
		'fullPath' => $fullPath,
		'type' => $type,
		'size' => filesize($fullPath),
		'mtime' => filemtime($fullPath),
		'mimetype' => $type === 'file' ? mime_content_type($filename) : null,
	);

	return $data;
}

if (!function_exists('mime_content_type')) {
	/**
	 * Get a file's mime type by its filename
	 *
	 * This is a bad version of http://php.net/mime_content_type which doesn't
	 * take the actual content of the file into question, only its name.
	 *
	 * @param string $filename The filename to consider
	 * @return string Mime type of file
	 */
	function mime_content_type($filename) {
		/**
		 * This is a mapping of regex patterns to types
		 */
		$mappings = array(
			// Archive/compression
			'/\.bz2$/i' => 'application/bzip2',
			'/\.bz$/i' => 'application/bzip',
			'/\.gz$/i' => 'application/gzip',
			'/\.tar$/i' => 'application/x-tar',
			'/\.zip$/i' => 'application/zip',

			// Image
			'/\.gif$/i' => 'image/gif',
			'/\.jpe?g$/i' => 'image/jpeg',
			'/\.png$/i' => 'image/png',
			'/\.svg$/i' => 'image/svg+xml',

			// Text
			'/\.html?$/i' => 'text/html',
			'/\.odt$/i' => 'application/vnd.oasis.opendocument.text',
			'/\.pdf$/i' => 'application/pdf',
			'/\.rtf$/i' => 'text/rtf',
			'/\.sh$/i' => 'text/x-shellscript',
			'/\.txt$/i' => 'text/plain',

			// Spreadsheet
			'/\.csv$/i' => 'text/csv',
			'/\.ods$/i' => 'application/vnd.oasis.opendocument.spreadsheet',

			// Presentation
			'/\.odp$/i' => 'application/vnd.oasis.opendocument.presentation',

			// Audio
			'/\.flac$/i' => 'audio/flac',
			'/\.midi?$/i' => 'audio/midi',
			'/\.mp3$/i' => 'audio/mpeg',
			'/\.ogg$/i' => 'audio/ogg',

			// Video
			'/\.mkv$/i' => 'video/x-matroska',
			'/\.mp4$/i' => 'video/mp4',

			// Font
			'/\.otf$/i' => 'application/x-font-opentype',
			'/\.ttf$/i' => 'application/x-font-truetype',
			'/\.woff2$/i' => 'font/woff2',
			'/\.woff$/i' => 'font/woff',

			// Executable
			'/\.exe$/i' => 'application/vnd.microsoft.portable-executable',
		);

		foreach ($mappings as $pattern => $mimetype) {
			if (preg_match($pattern, $filename)) {
				return $mimetype;
			}
		}

		// Unknown
		return null;
	}
}

/**
 * Get an icon file corresponding to a mime type
 *
 * @param string $mimetype Mime type, or the special case 'dir'
 * @return string Icon filename
 */
function mimeToIcon($mimetype) {
	$iconBase = '/usr/share/icons/gnome/32x32';
	$iconExtension = 'png';
	if ($mimetype === 'dir') {
		$icon = 'places/folder';
	} else if (file_exists(
		$iconBase
		. DIRECTORY_SEPARATOR . 'mimetypes'
		. DIRECTORY_SEPARATOR . str_replace('/', '-', $mimetype)
		. '.' . $iconExtension
	)) {
		$icon = 'mimetypes' .  DIRECTORY_SEPARATOR . str_replace('/', '-', $mimetype);
	} else if ($mimetype === 'text/x-shellscript') {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'text-x-script';
	} else if ($mimetype === 'image/svg+xml') {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'x-office-drawing';
	} else if ($mimetype === 'application/vnd.microsoft.portable-executable') {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'application-x-executable';
	} else if ($mimetype === 'application/vnd.oasis.opendocument.text' || $mimetype === 'text/rtf' || $mimetype === 'application/pdf') {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'x-office-document';
	} else if ($mimetype === 'application/vnd.oasis.opendocument.presentation') {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'x-office-presentation';
	} else if ($mimetype === 'application/vnd.oasis.opendocument.spreadsheet' || $mimetype === 'text/csv') {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'x-office-spreadsheet';
	} else if (preg_match('#^text/#', $mimetype)) {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'text-x-generic';
	} else if (preg_match('#^image/#', $mimetype)) {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'image-x-generic';
	} else if (preg_match('#^audio/#', $mimetype)) {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'audio-x-generic';
	} else if (preg_match('#^video/#', $mimetype)) {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'video-x-generic';
	} else if (preg_match('#^(application/x-font-|font/)#', $mimetype)) {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'font-x-generic';
	} else if (preg_match('#^application/(x-tar|[bg]?zip2?)$#', $mimetype)) {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'package-x-generic';
	} else {
		$icon = 'mimetypes' . DIRECTORY_SEPARATOR . 'gtk-file';
	}

	return $iconBase . DIRECTORY_SEPARATOR . $icon . '.' . $iconExtension;
}

/**
 * Return a data URI for a PNG image
 *
 * @param string $filename Filename of PNG file
 * @return string Data URI
 */
function dataUri($filename) {
	return 'data:image/png;base64,' . base64_encode(file_get_contents($filename));
}

/**
 * Sort entries
 *
 * This function looks for globals $sortBy, which is the key to use, and
 * $sortDir, which is the direction. $sortDir can be 'asc' or 'desc'. Numeric
 * sort is used for particular values of $sortBy, otherwise alphabetic.
 *
 * Directories are always sorted before files.
 *
 * @param array $fileData1 Structured data about first file
 * @param array $fileData2 Structured data about second file
 * @return int Integer less than, equal to, or greater than zero if the first
 * argument is considered to be respectively less than, equal to, or greater
 * than the second
 */
function sortEntries($fileData1, $fileData2) {
	global $sortBy, $sortDir;

	// Always sort directories before files unless sort is type
	if ($sortBy !== 'type') {
		if ($fileData1['type'] === 'dir' && $fileData2['type'] !== 'dir') {
			return -1;
		}
		if ($fileData1['type'] !== 'dir' && $fileData2['type'] === 'dir') {
			return 1;
		}
	}

	// Do the main sorting
	if (in_array($sortBy, array('mtime', 'size'))) {
		$cmp = $fileData1[$sortBy] - $fileData2[$sortBy];
	} else {
		$cmp = strcmp($fileData1[$sortBy], $fileData2[$sortBy]);
	}

	// Handle descending sort
	if ($sortDir === 'desc') {
		$cmp *= -1;
	}

	if ($cmp !== 0) {
		return $cmp;
	}

	// In ties, sort by filename ascending
	return strcmp($fileData1['filename'], $fileData2['filename']);
}

/**
 * Get a URL for this script with modified query parameters
 *
 * @param string $newSortBy Key to sort by
 * @return string URL
 */
function getUrl($newSortBy) {
	global $sortBy, $sortDir;

	$params = array(
		'sortby' => $newSortBy,
	);

	if ($newSortBy !== $sortBy) {
		// Sort key is changing; force direction to asc
		$params['sortdir'] = 'asc';
	} else {
		// Sort key stays the same; toggle direction
		$params['sortdir'] = $sortDir === 'desc' ? 'asc' : 'desc';
	}

	// Return new URL
	return preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']) . '?' . http_build_query($params);
}

/**
 * Display a filesize in human-readable form
 *
 * @param int $bytes Filesize in bytes
 * @return string Formatted filesize
 */
function humanSize($bytes) {
	if ($bytes === 0) {
		return '0';
	}

	$suffixes = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
	$factor = floor(log($bytes, 1024));
	return round($bytes / pow(1024, $factor), 1) . $suffixes[$factor];
}

// Get the directory path
$directoryPath = preg_replace('#/$#', '', preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']));
$fullDirectoryPath = $_SERVER['DOCUMENT_ROOT'] . $directoryPath;

// Get full list of entries except . and ..
$allEntries = array_filter(scandir($fullDirectoryPath), 'isInteresting');

// Get filtered list of entries
$entries = array_filter($allEntries, 'isDisplayable');

// Expand each entry to a data structure
$entries = array_map('fileData', $entries, array_fill(0, count($entries), $fullDirectoryPath));

// Sort entries
$sortBy = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 'filename';
$sortDir = isset($_REQUEST['sortdir']) ? $_REQUEST['sortdir'] : 'asc';
usort($entries, 'sortEntries');

// Display entries
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Index of <?php echo htmlspecialchars(dirname($_SERVER['PHP_SELF'])); ?></title>
		<style>
			.col-size {
				text-align: right;
			}
			thead tr, tr:nth-child(even) {
				background-color: whitesmoke;
			}
		</style>
	</head>
	<body>
		<?php if (dirname($directoryPath) !== ''): ?>
			<p>
				<a href="..">
					<img src="<?php echo dataUri(mimeToIcon('dir')); ?>" alt="" style="vertical-align: middle">
					Parent directory
				</a>
			</p>
		<?php endif ?>
		<table>
			<thead>
				<tr>
					<th>Icon</th>
					<th><a href="<?php echo htmlspecialchars(getUrl('filename')); ?>">Name</a></th>
					<th><a href="<?php echo htmlspecialchars(getUrl('size')); ?>">Size</a></th>
					<th><a href="<?php echo htmlspecialchars(getUrl('type')); ?>">Type</a></th>
					<th><a href="<?php echo htmlspecialchars(getUrl('mimetype')); ?>">Mime type</a></th>
					<th><a href="<?php echo htmlspecialchars(getUrl('mtime')); ?>">Modified</a></th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($entries)): foreach ($entries as $entry): ?>
					<tr>
						<td class="col-icon">
							<img src="<?php echo dataUri(mimeToIcon($entry['type'] === 'dir' ? 'dir' : $entry['mimetype'])); ?>" alt="">
						</td>
						<td class="col-filename">
							<a href="<?php echo htmlspecialchars($entry['filename']); ?>">
								<?php echo htmlspecialchars($entry['filename']); ?>
							</a>
						</td>
						<td class="col-size"><?php echo humanSize($entry['size']); ?></td>
						<td class="col-type"><?php echo $entry['type']; ?></td>
						<td class="col-mimetype"><?php echo $entry['mimetype']; ?></td>
						<td class="col-mtime"><?php echo date('Y-m-d H:i:s e', $entry['mtime']); ?></td>
					</tr>
				<?php endforeach; endif ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo count($entries); ?> entries
						<?php if (count($allEntries) > count($entries)): ?>
							(<?php echo count($allEntries) - count($entries); ?> hidden)
						<?php endif ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</body>
</html>
