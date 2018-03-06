<?php

namespace App\Http\Controllers\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Post;
use App\Model\MetaData;

class PostController extends Controller
{
    public function base()
    {
        // initiate return variables 
            $all_videos = [];
            $facebook_videos = [];
            $youtube_videos = [];
            $vimeo_videos = [];
        // Get 50 posts from post table
        $posts = Post::orderBy('post_date','desc')
                        ->where('post_status', 'publish')
                        ->where('post_type','avada_portfolio')
                        ->take(50)
                        ->get();
        
        foreach ($posts as $post) {

            // Get thumb_id from metadata table
            $metaData_thumb_id =  MetaData::where('post_id',$post->ID)
                                        ->where('meta_key','_thumbnail_id')
                                        ->value('meta_value');
            // Get pyre_video link from matadata table
            $video_url =  MetaData::where('post_id',$post->ID)
                                        ->where('meta_key','pyre_video')
                                        ->value('meta_value');
            // Get image name from matadata table
            $image_url =  MetaData::where('post_id',$metaData_thumb_id)
                                        ->where('meta_key',"_wp_attached_file")
                                        ->value('meta_value');
          
            // fileter url
            $one = str_after($video_url,'src=');
            $two = str_before($one,'frameborder=');
            $three = str_after($two,'"');
            $video_url = str_before($three,'"'); // Final video URL


            // Check for sepatate urls
            $has_url = str_contains($video_url,"http");
            if($has_url){
                $if_youtube_video = str_contains($video_url,"www.youtube.com");
                if($if_youtube_video){
                    $source_type = 'youtube';
                    $youtube_videos[] = $this->returnArray($post->ID,$post->post_date,$post->post_title,$post->post_name, $image_url, $video_url,$source_type);
                    // $youtube_videos = $retrun_array;
                }
                $if_facebook_video = str_contains($video_url,"www.facebook.com");
                if($if_facebook_video){
                    $source_type = 'facebook';
                    $facebook_videos[] = $this->returnArray($post->ID,$post->post_date,$post->post_title,$post->post_name, $image_url, $video_url,$source_type);
                    // $facebook_videos = $retrun_array;
                }
                $if_vimeo_video = str_contains($video_url,"vimeo");
                if($if_vimeo_video){
                    $source_type = 'vimeo';
                    $vimeo_videos[] = $this->returnArray($post->ID,$post->post_date,$post->post_title,$post->post_name, $image_url, $video_url,$source_type);
                    // $vimeo_videos = $retrun_array;
                }
    
    
                $all_videos[] = $this->returnArray($post->ID,$post->post_date,$post->post_title,$post->post_name, $image_url, $video_url,$source_type);
    

            }
            
            
        }
        
        return [$all_videos,$facebook_videos,$youtube_videos,$vimeo_videos];
        // return $all_videos;


    }

    private function returnArray($psot_id, $post_date, $post_title, $post_name, $image_url, $video_url,$source_type)
    {
         return  array(
                    'id' => $psot_id,
                    'post_date' => $post_date,
                    'post_title' => $post_title,
                    'post_url' => 'https://www.shraddha.lk/shraddha-programs/'.$post_name,
                    'image_url' => 'https://www.shraddha.lk/wp-content/uploads/'.$image_url,
                    'video_url' => $video_url,
                    'source_type' => $source_type,
                );
    }


    public function latest_videos_count($count=10)
    {
        $out=array_slice($this->base()[0], 0, $count);
        return response()->json($out);
    }

    public function facebook_videos($count=10)
    {
        $out=array_slice($this->base()[1], 0, $count);
        return response()->json($out);
    }

    public function youtube_videos($count=10)
    {
        $out=array_slice($this->base()[2], 0, $count);
        return response()->json($out);
    }
    public function vimeo_videos($count=10)
    {
        $out=array_slice($this->base()[3], 0, $count);
        return response()->json($out);
    }

}
        