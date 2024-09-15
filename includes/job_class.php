<?php
class Job
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createJob($data)
    {
        $query = "INSERT INTO jobs (name, campus,location,salary, posted_date, closing_date, job_description, job_requirement, benefits, job_type ,userID) VALUES (:name, :campus, :location, :salary, :posted_date, :closing_date, :job_description, :job_requirement, :benefits, :job_type, :uid)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($data);
    }

    public function getJobs()
    {
        $query = "SELECT * FROM jobs ORDER BY posted_date DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getJobsWithPagination($jobsPerPage,$offset){
        // Fetch jobs with limit and offset
        $stmt = $this->db->prepare("SELECT * FROM jobs ORDER BY posted_date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $jobsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    }
    public function getSpecificJobs($uid)
    {
        $query = "SELECT * FROM jobs where userID=$uid ORDER BY posted_date DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchJobs($search, $jobPerPage, $offset)
    {
        // Safely incorporate the LIMIT and OFFSET directly into the SQL query
        $stmt = $this->db->prepare("SELECT * FROM jobs WHERE name LIKE ? ORDER BY posted_date DESC LIMIT $jobPerPage OFFSET $offset");
        $stmt->execute(['%' . $search . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppledJobs($userid){
        $query = "SELECT * FROM job_applications where user_id=$userid";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
?>