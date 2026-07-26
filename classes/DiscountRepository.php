<?php

class DiscountRepository
{
    private mysqli $conn;


    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }



    /**
     * Get all available discounts
     */
    public function getDiscounts(): array
    {
        $sql = "
           SELECT 
           `discount_id`, 
           `discount_name`, 
           `discount_percentage`, 
           `created_at`, 
           `updated_at` 
           FROM `discounts` WHERE 1
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->execute();


        $result = $stmt->get_result();


        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
