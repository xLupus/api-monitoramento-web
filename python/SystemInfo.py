import psutil, platform

class SystemInfo:
    def get_cpu():
        return psutil.cpu_percent(interval=1)
            
    def get_system_name():
        return platform.uname().system
        
    def get_memory():
        svmem = psutil.virtual_memory()
        
        memory =  {
            'total_memory': svmem.total,
            'used_memory': svmem.used
        }
        
        return memory
    
    def get_discs():
        partitions = psutil.disk_partitions()

        discs = []
        
        for partition in partitions:
            try:
                partition_usage = psutil.disk_usage(partition.mountpoint)
                
            except PermissionError: # this can be catched due to the disk that isn't ready
                continue
            
            discs.append({
                'device': partition.device,
                'mountpoint': partition.mountpoint,
                'total_size': partition_usage.total,
                'used_size': partition_usage.used
            })
            
        return discs
        
    
    def get_temperature():
            return None;

        