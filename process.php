<?php
// process.php

include 'includes/utils.php';
include 'includes/db.php';
include 'templates/header.php';

function rankResumes($files, $conn) {
    $ranker = new Ranker();
    $resumes = $ranker->rankResumes($files);

    foreach ($resumes as $index => $resume) {
        // Insert ranking into the database
        $resumeId = getResumeIdFromPath($resume['path'], $conn);
        if ($resumeId) {
            $stmt = $conn->prepare("INSERT INTO rankings (resume_id, score, rank) VALUES (:resume_id, :score, :rank)");
            $stmt->execute([
                ':resume_id' => $resumeId,
                ':score' => $resume['score'],
                ':rank' => $index + 1
            ]);
        } else {
            echo "Resume ID not found for file: " . htmlspecialchars($resume['path']) . "<br>";
        }
    }

    return $resumes;
}

function getResumeIdFromPath($filePath, $conn) {
    $stmt = $conn->prepare("SELECT id FROM resumes WHERE file_path = :file_path");
    $stmt->execute([':file_path' => $filePath]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'] ?? null;
}

class Ranker {
    public function rankResumes($filePaths) {
        $resumes = [];

        foreach ($filePaths as $filePath) {
            $text = extractTextFromResume($filePath);
            $score = calculateResumeScore($text);
            $resumes[] = ['path' => $filePath, 'score' => $score];
        }

        usort($resumes, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return $resumes;
    }
}

$db = new Database();
$conn = $db->getConnection();
$uploadedFiles = isset($_SESSION['uploaded_files']) ? $_SESSION['uploaded_files'] : [];

// Debugging statement to check uploaded files
echo '<pre>';
print_r($uploadedFiles);
echo '</pre>';

if (!empty($uploadedFiles)) {
    $rankedResumes = rankResumes($uploadedFiles, $conn);
}
?>

<h2>Ranked Resumes</h2>
<?php if (isset($rankedResumes) && !empty($rankedResumes)): ?>
    <ol>
        <?php foreach ($rankedResumes as $resume): ?>
            <li><?php echo htmlspecialchars($resume['path']) . ' - Score: ' . $resume['score']; ?></li>
        <?php endforeach; ?>
    </ol>
<?php else: ?>
    <p>No resumes uploaded or ranking failed.</p>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>
